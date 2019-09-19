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
                if(!isset($marker['id']) || $marker['id'] == 0) continue;
                
                $markerStr .= "a:". ((int) $marker['id']) . ":" . $marker['colour'] . ";";
            }
        }
        if(isset($markerArray['player'])) {
            foreach($markerArray['player'] as $marker) {
                if(strlen($marker['colour']) != 6 || ! $this->checkHex($marker['colour'])) {
                    continue;
                }
                if(!isset($marker['id']) || $marker['id'] == 0) continue;
                
                $markerStr .= "p:". ((int) $marker['id']) . ":" . $marker['colour'] . ";";
            }
        }
        if(isset($markerArray['village'])) {
            foreach($markerArray['village'] as $marker) {
                if(strlen($marker['colour']) != 6 || ! $this->checkHex($marker['colour'])) {
                    continue;
                }
                if(!isset($marker['id']) || $marker['id'] == 0) continue;
                
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
                    $ally = Ally::ally($world->server->code, $world->name, $parts[1]);
                    $result[] = [
                        'id' => $ally->allyID,
                        'name' => BasicFunctions::decodeName($ally->name) . ' [' . BasicFunctions::decodeName($ally->tag) . ']',
                        'colour' => $parts[2],
                    ];
                    break;
                case 'p':
                    $player = Player::player($world->server->code, $world->name, $parts[1]);
                    $result[] = [
                        'id' => $player->playerID,
                        'name' => BasicFunctions::outputName($player->name),
                        'colour' => $parts[2],
                    ];
                    break;
                case 'v':
                    $vil = Village::village($world->server->code, $world->name, $parts[1]);
                    $result[] = [
                        'id' => $vil->villageID,
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
    
    public function getDefPlayerColour() {
        if(isset($this->defaultColours) && $this->defaultColours != null) {
            $parts = explode(";", $this->defaultColours);
            if(count($parts) == 3 && $this->checkHex($parts[1])) {
                return $parts[1];
            }
        }
        return $this->RGBToHex(MapGenerator::$DEFAULT_PLAYER_COLOUR);
    }
    
    public function getDefBarbarianColour() {
        if(isset($this->defaultColours) && $this->defaultColours != null) {
            $parts = explode(";", $this->defaultColours);
            if(count($parts) == 3 && $this->checkHex($parts[2])) {
                return $parts[2];
            }
        }
        return $this->RGBToHex(MapGenerator::$DEFAULT_BARBARIAN_COLOUR);
    }
    
    public function getBackgroundColour() {
        if(isset($this->defaultColours) && $this->defaultColours != null) {
            $parts = explode(";", $this->defaultColours);
            if(count($parts) == 3 && $this->checkHex($parts[0])) {
                return $parts[0];
            }
        }
        return $this->RGBToHex(MapGenerator::$DEFAULT_BACKGROUND_COLOUR);
    }
    
    public function setDefaultColours($background, $player, $barbarian) {
        $defCol = (($this->checkHex($background))?($background):(MapGenerator::$DEFAULT_BACKGROUND_COLOUR)) . ";";
        $defCol .= (($this->checkHex($player))?($player):(MapGenerator::$DEFAULT_PLAYER_COLOUR)) . ";";
        $defCol .= (($this->checkHex($barbarian))?($barbarian):(MapGenerator::$DEFAULT_BARBARIAN_COLOUR));
        $this->defaultColours = $defCol;
    }
    
    public function disableBarbarian() {
        if(!isset($this->defaultColours) || $this->defaultColours == null) {
            return;
        }
        
        $parts = explode(";", $this->defaultColours);
        $this->defaultColours = "{$parts[0]};{$parts[1]};null";
    }
    
    public function barbarianEnabled() {
        if(!isset($this->defaultColours) || $this->defaultColours == null) {
            return true;
        }
        
        $parts = explode(";", $this->defaultColours);
        return $parts[2] != "null";
    }
    
    public function disablePlayer() {
        if(!isset($this->defaultColours) || $this->defaultColours == null) {
            return;
        }
        
        $parts = explode(";", $this->defaultColours);
        $this->defaultColours = "{$parts[0]};null;{$parts[2]}";
    }
    
    public function playerEnabled() {
        if(!isset($this->defaultColours) || $this->defaultColours == null) {
            return true;
        }
        
        $parts = explode(";", $this->defaultColours);
        return $parts[1] != "null";
    }
    
    public function setDimensions($xs, $xe, $ys, $ye) {
        $dim = "" . ((int) $xs) . ";";
        $dim .= ((int) $xe) . ";";
        $dim .= ((int) $ys) . ";";
        $dim .= ((int) $ye);
        $this->dimensions = $dim;
    }
    
    public function getDimensions() {
        if(!isset($this->dimensions) || $this->dimensions == null) {
            return MapGenerator::$DEFAULT_DIMENSIONS;
        }
        $parts = explode(";", $this->dimensions);
        return [
            'xs' => (int) $parts[0],
            'xe' => (int) $parts[1],
            'ys' => (int) $parts[2],
            'ye' => (int) $parts[3],
        ];
    }
    
    /**
     * Configure given MapGenerator for rendering this map
     * @param \App\Util\MapGenerator $generator Generator to Configure
     */
    public function prepareRendering(MapGenerator $generator) {
        $generator->setLayerOrder([MapGenerator::$LAYER_MARK, MapGenerator::$LAYER_GRID]);
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
        
        if(isset($this->defaultColours) && $this->defaultColours != null) {
            $parts = explode(";", $this->defaultColours);
            if(count($parts) == 3) {
                $rgb = $this->hexToRGB($parts[0]);
                if($rgb != null) {
                    $generator->setBackgroundColour($rgb);
                }
                
                $rgb = $this->hexToRGB($parts[1]);
                if($rgb != null) {
                    $generator->setPlayerColour($rgb);
                }
                if($parts[1] == "null") {
                    $generator->setPlayerColour(null);
                }
                
                $rgb = $this->hexToRGB($parts[2]);
                if($rgb != null) {
                    $generator->setBarbarianColour($rgb);
                }
                if($parts[2] == "null") {
                    $generator->setBarbarianColour(null);
                }
            }
        }
        
        if(isset($this->dimensions) && $this->dimensions != null) {
            $parts = explode(";", $this->dimensions);
            if(count($parts) == 4) {
                $generator->setMapDimensions([
                    'xs' => $parts[0],
                    'xe' => $parts[1],
                    'ys' => $parts[2],
                    'ye' => $parts[3],
                ]);
            }
        }
    }
    
    private function checkHex($hex) {
        if(!ctype_alnum($hex)) return false;
        if(strlen($hex) != 6) return false;
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
    private function RGBToHex($rgb) {
        if(count($rgb) != 3) return null;
        $hexChars = "0123456789ABCDEF";
        
        $hex = $hexChars[intval(((int)$rgb[0]) / 16)];
        $hex .= $hexChars[((int)$rgb[0]) % 16];
        $hex .= $hexChars[intval(((int)$rgb[1]) / 16)];
        $hex .= $hexChars[((int)$rgb[1]) % 16];
        $hex .= $hexChars[intval(((int)$rgb[2]) / 16)];
        $hex .= $hexChars[((int)$rgb[2]) % 16];
        return $hex;
    }
}
