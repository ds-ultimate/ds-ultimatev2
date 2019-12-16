<?php

namespace App\Notifications;

use App\User;
use App\World;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use NotificationChannels\Discord\DiscordChannel;
use NotificationChannels\Discord\DiscordMessage;

class DiscordNotification extends Notification
{
    public $type;
    public $user;
    public $input;
    private $message;

    public function __construct($type, $user, $input)
    {
        $this->type = $type;
        $this->user = $user;
        $this->input = $input;
        $this->$type();
    }

    public function via($notifiable)
    {
        return [DiscordChannel::class];
    }

    public function toDiscord($notifiable)
    {
        if (config('services.discord.active') === 'ignore' OR config('services.discord.active') === true && config('app.debug') === true) {
            return DiscordMessage::create($this->message['content'], $this->message['embed']);
        }
    }

    public function exception()
    {
        /**
         * @var $input \Exception
         */
        $input = $this->input;
        $eMessage = $input->getMessage();
        if ($eMessage == ''){
            exit;
        }
        $ignore = [
            'Discord responded with an HTTP error: 429: You are being rate limited.',
            'Keine Daten über diesen Server \'js\' vorhanden.',
            'Unauthenticated.',
            'CSRF token mismatch.'
        ];
        if (!in_array($eMessage, $ignore)) {
            $trace = explode('#', $input->getTraceAsString());
            $this->message = [
                'content' => '**'.$eMessage.'**',
                'embed' => null,
            ];
//          $this->message = [
//              'content' => null,
//              'embed' => [
//                  'title' => nl2br($input->getMessage()),
//                  'description' => '#'.$trace[1].'
//                  #'.$trace[2].'
//                  #'.$trace[3].'
//                  ...',
//                  'color' => 13632027,
//                  'timestamp' => Carbon::now()->format('c'),
//                  'footer' => [
//                      'text' => '#ErrorException',
//                  ],
//              ],
//          ];
        }else{
            exit;
        }
    }

    public function worldUpdate(){
        /**
         * @var $world World
         */
        $input = $this->input;
        $world = $this->input['world'];
        $this->message = [
            'content' => '**'.$world->displayName().'**: Update Error '.$input['file'].' ('.$input['url'].')',
            'embed' => null,
        ];
//        $this->message = [
//            'content' => null,
//            'embed' => [
//                'title' => null,
//                'description' => '```css
//                ['.$world->displayName().']```: Update Error '.$input['file'].'
//                Dieses Datei konnte nicht geöffnet werden ['.$input['file'].']('.$input['url'].')',
//                'color' => 13632027,
//                'timestamp' => Carbon::now()->format('c'),
//                'footer' => [
//                    'text' => '#ErrorWorldUpdate',
//                ],
//            ],
//        ];
    }
}
