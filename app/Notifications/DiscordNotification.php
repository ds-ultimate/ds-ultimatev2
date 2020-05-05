<?php

namespace App\Notifications;

use App\Player;
use App\User;
use App\Util\BasicFunctions;
use App\Village;
use App\World;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
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
        return DiscordMessage::create($this->message['content'], $this->message['embed']);
    }

    public function exception()
    {
        /**
         * @var $input \Exception
         */
        $input = $this->input;
        $eMessage = $input->getMessage();
        $class = get_class($this->input);
//        $this->message = [
//            'content' => '``'.$class.'`` '.$eMessage,
//            'embed' => null,
//        ];
      $this->message = [
          'content' => '``'.$class.'`` '.$eMessage,
          'embed' => [
              'title' => mb_strimwidth(nl2br($input->getMessage()), 0, 200, "..."),
              'description' => $this->embedContent(),
              'color' => 13632027,
              'timestamp' => Carbon::now()->format('c'),
              'footer' => [
                  'text' => '#ErrorException',
              ],
          ],
      ];

    }

    public function worldUpdate(){
        /**
         * @var $world World
         */
        $input = $this->input;
        $world = $this->input['world'];
//        $this->message = [
//            'content' => '**'.$world->displayName().'**: Update Error '.$input['file'].' ('.$input['url'].')',
//            'embed' => null,
//        ];
        $this->message = [
            'content' => '**'.$world->displayName().'**: Update Error '.$input['file'].' ('.$input['url'].')',
            'embed' => [
                'title' => null,
                'description' => '```css
                ['.$world->displayName().']```: Update Error '.$input['file'].'
                Dieses Datei konnte nicht geöffnet werden ['.$input['file'].']('.$input['url'].')',
                'color' => 13632027,
                'timestamp' => Carbon::now()->format('c'),
                'footer' => [
                    'text' => '#ErrorWorldUpdate',
                ],
            ],
        ];
    }

    public function conquere(){
        $input = $this->input['conquere'];
        $world = $this->input['world'];
        $old = Player::player($world->server->code, $world->name, $input[3]);
        $new = Player::player($world->server->code, $world->name, $input[2]);
        $village = Village::village($world->server->code, $world->name, $input[0]);
        $time = Carbon::createFromTimestamp($input[1]);

        if (isset($old)){
            if ($old->ally_id == $new->ally_id){
                $color = 3447003;
            }else{
                $color = 3066993;
            }
        }else{
            $color = 9807270;
        }

        $this->message = [
//            'content' => 'Das Dorf ``['.$village->coordinates().']'.BasicFunctions::decodeName($village->name).'`` wurde geadelt um '.$input['date'].'. Alter Besitzer:``'.BasicFunctions::decodeName($old->name).'`` || Neuer Besitzer:``'.BasicFunctions::decodeName($new->name).'``',
            'content' => '',
            'embed' => [
                'title' => $world->displayName(),
                'color' => $color,
                'description' => 'Zeitpunkt: ``'.$time->format('d.m.Y H:i:s').'``',
                'fields' => [
                    ['name' => 'Alter Besitzer', 'value'=> self::conquerePlayer($world, $old, ':red_circle:'), 'inline' => true],
                    ['name' => "Dorf", 'value'=> '[['.$village->x.'|'.$village->y.'] '.BasicFunctions::decodeName($village->name).']('.route('village',[$world->server->code, $world->name, $village->villageID]).')'."\n \n \n ------------------------------", 'inline' => true],
                    ['name' => 'Neuer Besitzer', 'value'=> self::conquerePlayer($world, $new, ':green_circle:'), 'inline' => true],
                ],
                'timestamp' => Carbon::now()->toIso8601ZuluString(),
                'footer' => [
                    'text' => config('app.name'),
                ],
            ],
        ];
    }

    public static function conquerePlayer($world, $player, $icon){
        if (isset($player)){
            $output = $icon.' ['.BasicFunctions::decodeName($player->name).']('.route('player',[$world->server->code, $world->name, $player->playerID]).') ';
            if ($player->ally_id != 0){
                $output .= '[['.BasicFunctions::decodeName($player->allyLatest->tag).']]('.route('ally', [$world->server->code, $world->name, $player->allyLatest->allyID]).')';
            }
            $output .= "\n Punkte: ".BasicFunctions::numberConv($player->points)."\n Dörfer: ".BasicFunctions::numberConv($player->village_count);
        }else{
            $output = ':white_circle: '.__('ui.player.barbarian')."\n \n ";
        }
        return $output."\n ------------------------------";
    }

    public function embedContent(){
        $class = get_class($this->input);
        switch ($class){
            case 'Illuminate\Validation\ValidationException':
                $messageArray = $this->input->validator->messages()->messages();
                $msg = '';
                foreach ($messageArray as $key => $message){
                    $msg .= $key.': '.$message[0]."\n";
                }
                return $msg."\n".URL::current();
            case 'Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException':
                return URL::current();
            default:
                $trace = explode('#', $this->input->getTraceAsString());
                $traceStr = "";
                for($i = 0; $i < 4 && isset($trace[$i]); $i++) {
                    $traceStr .= "#{$trace[$i]}\n";
                }

                //TODO improve this...
                try {
                    $traceStr .= URL::current();
                } catch (\Exception $ex) {
                }
                return $traceStr;
        }
    }
}
