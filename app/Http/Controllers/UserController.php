<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{   
    // Handle validate & store registration data
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ], [
            'email.unique' => 'Email sudah terdaftar.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal!',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil ditambahkan!',
            'user' => $user,
        ], 201);
    }

    // Handle validate login data
    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => false, 
                'message' => 'Validasi gagal!',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if(!$user || !Hash::check($request->password, $user->password)){
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah'
            ], 401);
        }

        $token = $user->createToken('authToken')->plainTextToken;

        return response ()->json([
            'success' => true,
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token
        ], 200);
    }

    // Handle validate & update profile
    public function updateProfile(Request $request, string $id){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$id,
        ], [
            'email.unique' => 'Email sudah terdaftar.',
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal!',
                'errors' => $validator->errors()], 422);
        }

        $user = User::find($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile berhasil diupdate!',
            'user' => $user,
        ], 200);
    }

    // Handle validate & update password
    public function updatePassword(Request $request, string $id){

        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8',
            'new_password' => 'required|string|min:8',
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => false, 
                'message' => 'Validasi gagal!', 
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::find($id);
        if(!Hash::check($request->password, $user->password)){
            return response()->json([
                'success' => false,
                'message' => 'Password lama salah'
            ], 401);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil di update',
            'user' => $user,
        ], 200);
    }

    // Handle save token (For Expo Push Notification)
    public function saveToken(Request $request){
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'expo_push_token' => 'required|string'
        ]);
      
        if(!$request->user_id){
            return response()->json([
                'success' => false,
                'message' => 'User ID is required'
            ], 422);
        }
        if(!$request->expo_push_token){
            return response()->json([
                'success' => false,
                'message' => 'Expo Push Token is required'
            ]);
        }
      
        $user = User::find($request->user_id);
      
        $user->expo_push_token = $request->expo_push_token;
        $user->save();
      
        return response()->json(['message' => 'Token saved successfully']);
    }
}
