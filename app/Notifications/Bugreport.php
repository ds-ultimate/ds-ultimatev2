<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class Bugreport extends Notification
{
    use Queueable;

    public $bugreport;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(\App\Bugreport $bugreport)
    {
        $this->bugreport = $bugreport;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->greeting($this->bugreport->title.' ['.$this->bugreport->getPriority().']')
                    ->line('Von: '.$this->bugreport->name.' ['.$this->bugreport->email.']')
                    ->line($this->bugreport->description);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
