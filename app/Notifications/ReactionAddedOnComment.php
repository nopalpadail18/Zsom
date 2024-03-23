<?php

namespace App\Notifications;

use App\Models\Comments;
use App\Models\Posts;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReactionAddedOnComment extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Posts $post, public Comments $comment, public User $user)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('User "' . $this->user->username . '" reacted on your comment. Your comment:')
            ->line('"' . $this->comment->comment . '"')
            ->action('Notification Action', url(route('post.view', $this->post->id)))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
