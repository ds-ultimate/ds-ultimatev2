<?php

namespace App\Tool\Map;

use App\Ally;
use App\Player;
use App\Village;
use App\World;
use App\Util\MapGenerator;
use App\Util\BasicFunctions;
use Illuminate\Database\Eloquent\Model;

class Map extends Model
{
    protected $table = 'map';
    protected $connection = 'mysql';

    protected $dates = [
        'created_at',
        'updated_at',
    ];
    
    protected $hidden = [
        'edit_key',
        'show_key',
    ];

    protected $fillable = [
        'id',
        'world_id',
        'user_id',
        'edit_key',
        'show_key',
        'markers',
        'opaque',
        'skin',
        'layers',
        'dimensions',
        'defaultColours',
    ];

    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }

    public function world(){
        return $this->belongsTo('App\World', 'world_id');
    }
    
    /**
     * Save format: {type}:{id}:{colour (hex)};...;...
     * @param type $markerArray
     */
    public function setMarkers($markerArray) {
        $markerStr = "";
        if(isset($markerArray['ally'])) {
            foreach($markerArray['ally'] as $marker) {
                if(strlen($marker['colour']) != 6 || ! $this->checkHex($marker['colour'])) {
                    continue;
                }
                if($marker['id'] == 0) continue;
                
                $markerStr .= "a:". ((int) $marker['id']) . ":" . $marker['colour'] . ";";
            }
        }
        if(isset($markerArray['player'])) {
            foreach($markerArray['player'] as $marker) {
                if(strlen($marker['colour']) != 6 || ! $this->checkHex($marker['colour'])) {
                    continue;
                }
                if($marker['id'] == 0) continue;
                
                $markerStr .= "p:". ((int) $marker['id']) . ":" . $marker['colour'] . ";";
            }
        }
        if(isset($markerArray['village'])) {
            foreach($markerArray['village'] as $marker) {
                if(strlen($marker['colour']) != 6 || ! $this->checkHex($marker['colour'])) {
                    continue;
                }
                if($marker['id'] == 0) continue;
                
                $markerStr .= "v:". ((int) $marker['id']) . ":" . $marker['colour'] . ";";
            }
        }
        
        $this->markers = $markerStr;
    }
    
    public function getMarkersAsDefaults(World $world, $filterBy) {
        $result = array();
        foreach(explode(";", $this->markers) as $marker) {
            $parts = explode(":", $marker);
            if(count($parts) != 3) continue;
            if($parts[0] != $filterBy) continue;
            
            switch($parts[0]) {
                case 'a':
                    $result[] = [
                        'name' => BasicFunctions::outputName(Ally::ally($world->server->code, $world->name, $parts[1])->name),
                        'colour' => $parts[2],
                    ];
                    break;
                case 'p':
                    $result[] = [
                        'name' => BasicFunctions::outputName(Player::player($world->server->code, $world->name, $parts[1])->name),
                        'colour' => $parts[2],
                    ];
                    break;
                case 'v':
                    $vil = Village::village($world->server->code, $world->name, $parts[1]);
                    $result[] = [
                        'x' => $vil->x,
                        'y' => $vil->y,
                        'colour' => $parts[2],
                    ];
                    break;
            }
        }
        
        //append one empty row
        $result[] = null;
        return $result;
    }
    
    /**
     * Configure given MapGenerator for rendering this map
     * @param \App\Util\MapGenerator $generator Generator to Configure
     */
    public function prepareRendering(MapGenerator $generator) {
        $generator->setLayerOrder([MapGenerator::$LAYER_MARK]);
        if(isset($this->markers) && $this->markers != null) {
            foreach(explode(";", $this->markers) as $marker) {
                $parts = explode(":", $marker);
                if(count($parts) != 3) continue;

                $rgb = $this->hexToRGB($parts[2]);
                switch($parts[0]) {
                    case 'a':
                        $generator->markAlly($parts[1], $rgb);
                        break;
                    case 'p':
                        $generator->markPlayer($parts[1], $rgb);
                        break;
                    case 'v':
                        $generator->markVillage($parts[1], $rgb);
                        break;
                }
            }
        }
    }
    
    private function checkHex($hex) {
        if(!ctype_alnum($hex)) return false;
        $validHex = "0123456789ABCDEF";
        
        for($i = 0; $i < strlen($hex); $i++) {
            if(strpos($validHex, $hex[$i]) === false) {
                return false;
            }
        }
        return true;
    }
    
    private function hexToRGB($hex) {
        if(! $this->checkHex($hex)) return null;
        if(strlen($hex) != 6) return null;
        $hexChars = "0123456789ABCDEF";
        
        return [
            strpos($hexChars, $hex[0]) * 16 + strpos($hexChars, $hex[1]),
            strpos($hexChars, $hex[2]) * 16 + strpos($hexChars, $hex[3]),
            strpos($hexChars, $hex[4]) * 16 + strpos($hexChars, $hex[5]),
        ];
    }
}
