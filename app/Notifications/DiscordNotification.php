<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use NotificationChannels\Discord\DiscordChannel;
use NotificationChannels\Discord\DiscordMessage;

class DiscordNotification extends Notification
{
    public $elm;
    public $user;
    private $message;

    public function __construct(DiscordNotificationQueueElement $queueElm)
    {
        $this->elm = $queueElm;
        $this->message = $queueElm->getMessage();
    }

    public function via($notifiable)
    {
        return [DiscordChannel::class];
    }

    public function toDiscord($notifiable)
    {
        return DiscordMessage::create($this->message['content'], $this->message['embed']);
    }
}
