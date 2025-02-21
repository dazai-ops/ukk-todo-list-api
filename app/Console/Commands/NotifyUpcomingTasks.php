<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Task;
use App\Models\User;
use App\Jobs\SendPushNotificationJob;

class NotifyUpcomingTasks extends Command
{
    protected $signature = 'notify:upcoming-tasks';
    protected $description = 'Mengirim notifikasi ke user jika tugas mendekati deadline dalam 10 menit';

    public function handle()
    {
        // Get current time (Jakarta version brow, wkwkwkw)
        $now = Carbon::now('Asia/Jakarta');
        $deadlineThreshold = Carbon::now('Asia/Jakarta')->addMinutes(10);

        // Log activity (Untuk apa yak?)
        // Buat diliat di laravel.log lahh
        Log::info('NotifyUpcomingTasks - Mulai menjalankan command', [
            'now' => $now->toDateTimeString(),
            'deadlineThreshold' => $deadlineThreshold->toDateTimeString(),
        ]);

        // Get all tasks that are due within 10 minutes
        $tasks = Task::where('deadline_date', $now->toDateString())
                     ->whereBetween('deadline_time', [
                        $now->format('H:i:s'), 
                        $deadlineThreshold->format('H:i:s')
                     ])
                     ->where('status', '!=', 'done')
                     ->where('notified', 0)
                     ->get();
        
        // Log activity (result tasks dari query di atas) 
        Log::info('NotifyUpcomingTasks - Task ditemukan', [
            'task_count' => $tasks->count(),
            'tasks' => $tasks->toArray(),
        ]);

        // Send notifications (Bulk send)
        foreach ($tasks as $task) {
            $user = User::find($task->user_id);
            if ($user && $user->expo_push_token) {

                // Log activity (user_id, task_id, expo_push_token)
                // For validate usernya & token expo push available YGY
                Log::info('NotifyUpcomingTasks - Mengirim notifikasi', [
                    'user_id' => $user->id,
                    'task_id' => $task->id,
                    'expo_push_token' => $user->expo_push_token,
                ]);

                // Send notification
                // Use this Job for handle "send notification"
                SendPushNotificationJob::dispatch(
                    $user->expo_push_token, 
                    "Tugas '{$task->judul}' akan segera berakhir dalam 10 menit!"
                );

                // Update status "notified" 1 (sekali panggil)
                $task->update(['notified' => 1]);
            } else {
                // Log activity
                // Buat diliat di laravel.log
                Log::warning('NotifyUpcomingTasks - User tidak ditemukan atau tidak memiliki expo_push_token', [
                    'task_id' => $task->id,
                    'user_id' => $task->user_id,
                ]);
            }
        }

        $this->info('Notifikasi telah dikirim.');
        Log::info('NotifyUpcomingTasks - Command selesai');
    }
}
