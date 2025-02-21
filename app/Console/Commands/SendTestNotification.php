<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Jobs\SendPushNotificationJob;

class SendTestNotification extends Command
{
    protected $signature = 'notify:test';
    protected $description = 'Mengirim notifikasi push test ke user tertentu';

    public function handle()
    {
        // Testing notification single device by user_id
        $user = User::find(1);

        if ($user) {
            SendPushNotificationJob::dispatch($user->expo_push_token, "Tes");
            $this->info('Notifikasi test berhasil dikirim.');
        } else {
            $this->error('User tidak ditemukan.');
        }
    }
}
