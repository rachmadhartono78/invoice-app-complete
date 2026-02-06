<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class GeneralNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    public $user;

    public string $title;

    public string $message;

    public $url_action;

    public $parameter_action = [];

    public $typeNotification;

    /** 
     * Create a new notification instance.
     */
    public function __construct($user, $title, $message, $url_action = 'tes', $parameter_action = [], $type = 'notification')
    {
        $this->user = $user;
        $this->title = $title;
        $this->message = $message;
        $this->url_action = $url_action;
        $this->parameter_action = $parameter_action;
        $this->typeNotification = $type ?? 'toast';

    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['broadcast', 'database'];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'id' => $this->id,
            'title' => $this->title,
            'message' => $this->message,
            'url_action' => $this->url_action,
            'parameter_action' => $this->parameter_action,
            'show_as' => $this->typeNotification,
            'created_at' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Get the array representation of the notification for database storage.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'url_action' => $this->url_action,
            'parameter_action' => $this->parameter_action,
            'show_as' => $this->typeNotification,
            'sender' => $this->user->name,
            'avatar_sender' => $this->user->avatar_url,
        ];
    }
}
