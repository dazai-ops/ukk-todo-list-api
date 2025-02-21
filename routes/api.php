<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TaskController;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Endpoint Authentication
Route::resource('/users', UserController::class);
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

// Endpoint Task Manajemen
Route::middleware('auth:sanctum')->post('/tasks', [TaskController::class, 'store']);
Route::get('/tasks/user/{id}', [TaskController::class, 'taskList']);
Route::put('/task/{id}', [TaskController::class, 'update']);
Route::delete('/task/{id}', [TaskController::class, 'destroy']);
Route::put('/task/{id}/status', [TaskController::class, 'updateStatus']);
Route::put('/task/{id}/priority', [TaskController::class, 'updatePriority']);

// Endpoint User Profile & Push Notification
Route::put('/user/{id}', [UserController::class, 'updateProfile']);
Route::put('/user/password/{id}', [UserController::class, 'updatePassword']);
Route::put('/save-token', [UserController::class, 'saveToken']);
