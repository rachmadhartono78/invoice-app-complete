<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $user;

    protected $message;

    protected $title;

    protected $url_action;

    protected $parameter_action = [];

    public function __construct($user, $title, $message, $url_action = 'tes', $parameter_action = [])
    {
        $this->user = $user;
        $this->message = $message;
        $this->title = $title;
        $this->url_action = $url_action;
        $this->parameter_action = $parameter_action;

    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function broadcastOn()
    {
        return ['notification-channel'];
    }

    public function broadcastAs()
    {
        return 'new-notification';
    }

    public function toArray($notifiable)
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'url_action' => $this->url_action,
            'parameter_action' => $this->parameter_action,
            'user' => $this->user,
        ];
    }

    public function toBroadcast($notifiable)
    {

        return [
            'title' => $this->title,
            'message' => $this->message,
            'url_action' => $this->url_action,
            'parameter_action' => $this->parameter_action,
            'user' => $this->user,
        ];

    }
}
