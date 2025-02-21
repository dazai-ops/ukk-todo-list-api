<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{

    // Store new data Task
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'judul' => 'required',
            'detail' => 'required',
            'deadline_date' => 'required',
            'deadline_time' => 'required',
            'prioritas' => 'required|in:low,medium,high',
            'status' => 'required|in:open,in_progress,done',
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => 'Validasi data gagal!',
                'errors' => $validator->errors()
            ], 422);
        }

        $task = Task::create([
            'user_id' => Auth::id(),
            'judul' => $request->judul,
            'detail' => $request->detail,
            'deadline_date' => $request->deadline_date,
            'deadline_time' => $request->deadline_time,
            'prioritas' => $request->prioritas,
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Task berhasil dibuat!',
            'task' => $task
        ], 201);
    }

    // Update data Task
    public function update(Request $request, string $id){
        $task = Task::find($id);

        if(!$task){
            return response()->json([
                'success' => false,
                'message' => 'Task tidak ditemukan!'
            ], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'detail' => 'required|string',
            'deadline_date' => 'required',
            'deadline_time' => 'required',
            'prioritas' => 'required|in:low,medium,high',
            'status' => 'required|in:open,in_progress,done',
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => 'Validasi data gagal!',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $task->update([
            'judul' => $request->judul,
            'detail' => $request->detail,
            'deadline_date' => $request->deadline_date,
            'deadline_time' => $request->deadline_time,
            'prioritas' => $request->prioritas,
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Task berhasil diperbarui!',
            'task' => $task
        ], 200);
    }

    // Delete (Destroy) data Task
    // Is Softdelete = true
    public function destroy(string $id){
        $task = Task::find($id);

        if(!$task){
            return response()->json([
                'message' => 'Task tidak ditemukan',
            ], 404);
        }

        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'Task berhasil dihapus!',
        ], 200);
    }

    // Update "Status" task
    public function updateStatus(Request $request, string $id){
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:open,in_progress,done',
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan',
                'errors' => "$validator->errors()"
            ], 422);
        }

        $task = Task::find($id);

        if(!$task){
            return response()->json([
                'message' => 'Task tidak ditemukan',
            ], 404);
        }

        $task->status = $request->status;
        $task->save();

        return response()->json([
            'message' => 'Status task berhasil diperbarui!',
            'task' => $task,
        ], 200);
    }

    // Update "Priority" task
    public function updatePriority(Request $request, string $id){
        $validator = Validator::make($request->all(), [
            'priority' => 'required|in:low,medium,high',
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan',
                'errors' => "$validator->errors()"
            ], 422);
        }

        $task = Task::find($id);

        if(!$task){
            return response()->json([
                'message' => 'Task tidak ditemukan',
            ], 404);
        }

        $task->prioritas = $request->priority;
        $task->save();

        return response()->json([
            'message' => 'Prioritas task berhasil diperbarui!',
            'task' => $task,
        ], 200);
    }

    // Get all data Task (by user_id)
    public function taskList($id){
        $task = Task::where('user_id', $id)
                ->orderBy('deadline_date', 'asc')
                ->get();

        if($task->isEmpty()){
            return response()->json([
                'success' => false,
                'message' => 'Task tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Task berhasil didapatkan!',
            'tasks' => $task
        ]);
    }
}
