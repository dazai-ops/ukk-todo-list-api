<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendPushNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $expoToken;
    protected $message;

    // Constructor (Keep Expo Push Token & Message from (NotifyUpcomingTasks.php))
    public function __construct($expoToken, $message){
        $this->expoToken = $expoToken;
        $this->message = $message;
    }

    public function handle(){
        if (!is_string($this->expoToken)) {
            Log::error('Token Expo tidak valid.', ['expoToken' => $this->expoToken]);
            return;
        }
        // Send request to Expo API Service
        $response = Http::post('https://exp.host/--/api/v2/push/send', [
            'to' => $this->expoToken,
            'title' => 'Peringatan Tugas',
            'body' => $this->message,
            'sound' => 'default',
            'priority' => 'high'
        ]);

        // Log activity
        if ($response->successful()) {
            Log::info('Notifikasi push berhasil dikirim.');
        } else {
            Log::error('Gagal mengirim notifikasi push.', [
                'status_code' => $response->status(),
                'response' => $response->body()
            ]);
        }
    }

}
