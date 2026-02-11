<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\NewMessageNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;

    protected $notificationData;

    public function __construct($userId, $notificationData)
    {
        $this->userId = $userId;
        $this->notificationData = $notificationData;
    }

    public function handle(): void
    {
        try {
            $user = User::find($this->userId);

            if ($user) {
                $user->notify(new NewMessageNotification(
                    $user,
                    $this->notificationData['title'],
                    $this->notificationData['message'],
                    $this->notificationData['url'],
                    $this->notificationData['data']
                ));
            }
        } catch (\Exception $e) {
            Log::warning('Failed to send notification to user_id '.$this->userId.': '.$e->getMessage());
        }
    }
}
