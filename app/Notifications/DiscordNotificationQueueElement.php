<?php

namespace App\Notifications;

use App\Log;
use App\World;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

class DiscordNotificationQueueElement extends Model
{
    public $table = "discord_bot_notifications";
    
    protected $fillable = [
        'notification_data',
    ];

    public $timestamps = [
        'created_at',
        'updated_at',
    ];
    
    public function send() {
        Notification::send(new Log(), new DiscordNotification($this));
        $this->delete();
    }
    
    public function getMessage() {
        $parts = explode(";", $this->notification_data);
        
        $result = [];
        foreach($parts as $part) {
            $keyVal = explode("|", $part);
            $key = $keyVal[0];
            $val = base64_decode($keyVal[1]);
            static::recursiveDecode($key, $val, $result);
        }
        return $result;
    }
    
    private static function recursiveDecode($key, $val, &$result) {
        $idx = strpos($key, "/");
        if($idx === false) {
            //not found -> finished
            $result[$key] = $val;
        } else {
            $keyNew = substr($key, $idx + 1);
            static::recursiveDecode($keyNew, $val, $result[substr($key, 0, $idx)]);
        }
    }
    
    public static function worldUpdate(World $world, $file, $url) {
        $worldName = $world->displayName();
        $message = [
            'content' => '**'.$worldName."**: Update Error $file ($url)",
            'embed' => [
                'title' => null,
                'description' => '```css
                ['.$worldName."]```: Update Error $file\n".
                "Die Datei konnte nicht geöffnet werden [$file]($url)",
                'color' => 13632027,
                'timestamp' => Carbon::now()->format('c'),
                'footer' => [
                    'text' => '#ErrorWorldUpdate',
                ],
            ],
        ];
        
        static::encodeMessage($message);
    }
    
    public static function exception($exception)
    {
        $eMessage = $exception->getMessage();
        $class = get_class($exception);
        
        $message = [
            'content' => '``'.$class.'`` '.mb_strimwidth($eMessage, 0, 150, "..."),
            'embed' => [
                'title' => mb_strimwidth(nl2br($eMessage), 0, 200, "..."),
                'description' => static::embedContent($class, $exception),
                'color' => 13632027,
                'timestamp' => Carbon::now()->format('c'),
                'footer' => [
                    'text' => '#ErrorException',
                ],
            ],
        ];
        
        static::encodeMessage($message);
    }
    
    public function conquere(User $user, $world, $conquerArr){
        $old = Player::player($world->server->code, $world->name, $conquerArr['old_owner']);
        $new = Player::player($world->server->code, $world->name, $conquerArr['new_owner']);
        $village = Village::village($world->server->code, $world->name, $conquerArr['village_id']);
        $time = Carbon::createFromTimestamp($conquerArr['timestamp']);

        if (isset($old)){
            if ($old->ally_id == $new->ally_id){
                $color = 3447003;
            }else{
                $color = 3066993;
            }
        }else{
            $color = 9807270;
        }

        $message = [
//            'content' => 'Das Dorf ``['.$village->coordinates().']'.BasicFunctions::decodeName($village->name).'`` wurde geadelt um '.$input['date'].'. Alter Besitzer:``'.BasicFunctions::decodeName($old->name).'`` || Neuer Besitzer:``'.BasicFunctions::decodeName($new->name).'``',
            'content' => '',
            'embed' => [
                'title' => $world->displayName(),
                'color' => $color,
                'description' => 'Zeitpunkt: ``'.$time->format('d.m.Y H:i:s').'``',
                'fields' => [
                    ['name' => 'Alter Besitzer', 'value'=> self::conquerePlayer($world, $old, ':red_circle:'), 'inline' => true],
                    ['name' => "Dorf", 'value'=> '[['.$village->x.'|'.$village->y.'] '.BasicFunctions::decodeName($village->name).']('.route('village',[$world->server->code, $world->name, $village->villageID]).')'."\n Punkte: ".BasicFunctions::numberConv($village->points)."\n \n ------------------------------", 'inline' => true],
                    ['name' => 'Neuer Besitzer', 'value'=> self::conquerePlayer($world, $new, ':green_circle:'), 'inline' => true],
                ],
                'timestamp' => $time->toIso8601ZuluString(),
                'footer' => [
                    'text' => config('app.name'),
                    'icon_url' => 'https://cdn.discordapp.com/app-icons/654376022128459796/dda48d5acf94cb4e7d45753827c4ebab.png'
                ],
            ],
        ];
        
        static::encodeMessage($message);
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

    public static function embedContent($class, $exception){
        switch ($class){
            case 'Illuminate\Validation\ValidationException':
                $messageArray = $exception->validator->messages()->messages();
                $msg = '';
                foreach ($messageArray as $key => $message){
                    $msg .= $key.': '.$message[0]."\n";
                }
                return mb_strimwidth($msg."\n".URL::current(), 0, 200, "...");
            case 'Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException':
                return mb_strimwidth(URL::current(), 0, 200, "...");
            default:
                $trace = explode('#', $exception->getTraceAsString());
                $traceStr = "";
                for($i = 0; $i < 10 && isset($trace[$i]); $i++) {
                    $traceStr .= "#{$trace[$i]}\n";
                }

                $traceStr =  mb_strimwidth($traceStr, 0, 800, "...");
                //TODO improve this...
                try {
                    $traceStr .= "\n" . URL::current();
                } catch (\Exception $ex) {
                }
                return mb_strimwidth($traceStr, 0, 1000, "...");
        }
    }
    
    private static function encodeMessage($message) {
        $encoded = static::recursiveEncode($message);
        $asData = "";
        $first = true;
        foreach($encoded as $dat) {
            if(!$first) $asData .= ";";
            $asData .= "{$dat[0]}|{$dat[1]}";
            $first = false;
        }
        
        $model = new DiscordNotificationQueueElement();
        $model->notification_data = $asData;
        $model->save();
    }
    
    private static function recursiveEncode($message, $prefix="") {
        if(gettype($message) == "array") {
            $ret = [];
            foreach($message as $key => $item) {
                if(gettype($item) == "array") {
                    $ret = array_merge($ret, static::recursiveEncode($item, "$prefix$key/"));
                } else if(static::isString($item)) {
                    $ret[] = ["$prefix$key", base64_encode($item)];
                } else {
                    $ret[] = ["$prefix$key", base64_encode("not an string " . gettype($item))];
                }
            }
            return $ret;
        } else if(static::isString($message)) {
            return base64_encode($message);
        } else {
            return base64_encode("not an string " . gettype($message));
        }
    }
    
    private static function isString($data) {
        return  ( !is_array( $data ) ) &&
                ( ( !is_object( $data ) && settype( $data, 'string' ) !== false ) ||
                ( is_object( $data ) && method_exists( $data, '__toString' ) ) );
    }
}
