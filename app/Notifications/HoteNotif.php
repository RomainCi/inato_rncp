<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use PhpParser\Node\Expr\Cast\Object_;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class HoteNotif extends Notification
{
    // use Queueable;
    public $invitation;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($invitation)
    {
        $this->invitation = $invitation;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     */
    public function toArray($notifiable)
    {
        return $this->invitation->id;
    }
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage(["invitationAdmin" => $this->invitation]);
    }
}
