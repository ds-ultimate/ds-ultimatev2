<?php

namespace App\Tool\Map;

use App\Ally;
use App\Player;
use App\Village;
use App\World;
use App\Util\AbstractMapGenerator;
use App\Util\BasicFunctions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Map extends Model
{
    use SoftDeletes;

    protected $table = 'map';
    protected $connection = 'mysql';

    protected $dates = [
        'cached_at',
        'created_at',
        'updated_at',
        'deleted_at',
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
        'title',
        'drawing_obj',
        'drawing_dim',
        'drawing_png',
        'markerFactor',
        'continentNumbers',
        'shouldUpdate',
    ];

    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }

    public function world(){
        return $this->belongsTo('App\World', 'world_id');
    }

    public function follows(){
        return $this->morphToMany('App\User', 'followable', 'follows');
    }

    public function getTitle() {
        if($this->title == null || $this->title == "") {
            return ucfirst(__('tool.map.title'));
        }
        return $this->title;
    }

    /**
     * Save format: {type}:{id}:{colour (hex)}:{settings (t for text)};...;...
     * @param type $markerArray
     */
    public function setMarkers($markerArray) {
        $markerStr = "";

        $types = [
            array("ally", "a"),
            array("player", "p"),
            array("village", "v"),
        ];

        foreach($types as $type) {
            if(!isset($markerArray[$type[0]])) continue;

            foreach($markerArray[$type[0]] as $marker) {
                if(strlen($marker['colour']) != 6 || ! Map::checkHex($marker['colour'])) {
                    continue;
                }
                if(!isset($marker['id']) || $marker['id'] == 0) continue;


                $markerStr .= $type[1] . ":". ((int) $marker['id']) . ":" . $marker['colour'];

                $markerOptions = "";
                if(isset($marker['textHere']) && isset($marker['text']) && $marker['text'] == "on") {
                    $markerOptions .= "t";
                }
                if(isset($marker['hLightHere']) && isset($marker['hLight']) && $marker['hLight'] == "on") {
                    $markerOptions .= "h";
                }

                if(strlen($markerOptions) > 0) {
                    $markerStr .= ":" . $markerOptions;
                }
                $markerStr .= ";";
            }
        }

        $this->markers = $markerStr;
    }

    public function getMarkersAsDefaults(World $world, $filterBy) {
        $result = array();
        foreach(explode(";", $this->markers) as $marker) {
            $parts = explode(":", $marker);
            if(count($parts) < 3 || count($parts) > 4) continue;
            if($parts[0] != $filterBy) continue;

            switch($parts[0]) {
                case 'a':
                    $ally = Ally::ally($world->server->code, $world->name, $parts[1]);
                    if($ally == null) break;
                    $result[] = [
                        'id' => $ally->allyID,
                        'name' => BasicFunctions::decodeName($ally->name) . ' [' . BasicFunctions::decodeName($ally->tag) . ']',
                        'colour' => $parts[2],
                        'text' => count($parts) > 3 && strpos($parts[3], "t") !== false,
                        'highlight' => count($parts) > 3 && strpos($parts[3], "h") !== false,
                    ];
                    break;
                case 'p':
                    $player = Player::player($world->server->code, $world->name, $parts[1]);
                    if($player == null) break;
                    $result[] = [
                        'id' => $player->playerID,
                        'name' => BasicFunctions::decodeName($player->name),
                        'colour' => $parts[2],
                        'text' => count($parts) > 3 && strpos($parts[3], "t") !== false,
                        'highlight' => count($parts) > 3 && strpos($parts[3], "h") !== false,
                    ];
                    break;
                case 'v':
                    $vil = Village::village($world->server->code, $world->name, $parts[1]);
                    if($vil == null) break;
                    $result[] = [
                        'id' => $vil->villageID,
                        'x' => $vil->x,
                        'y' => $vil->y,
                        'name' => $vil->name,
                        'owner' => $vil->playerLatest,
                        'colour' => $parts[2],
                        'text' => count($parts) > 3 && strpos($parts[3], "t") !== false,
                        'highlight' => count($parts) > 3 && strpos($parts[3], "h") !== false,
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
            if(count($parts) == 3 && Map::checkHex($parts[1])) {
                return $parts[1];
            }
        }
        return $this->getDefResetPlayerColour();
    }

    public function getDefResetPlayerColour() {
        return Map::RGBToHex(AbstractMapGenerator::$DEFAULT_PLAYER_COLOUR);
    }

    public function getDefBarbarianColour() {
        if(isset($this->defaultColours) && $this->defaultColours != null) {
            $parts = explode(";", $this->defaultColours);
            if(count($parts) == 3 && Map::checkHex($parts[2])) {
                return $parts[2];
            }
        }
        return $this->getDefResetBarbarianColour();
    }

    public function getDefResetBarbarianColour() {
        return Map::RGBToHex(AbstractMapGenerator::$DEFAULT_BARBARIAN_COLOUR);
    }

    public function getBackgroundColour() {
        if(isset($this->defaultColours) && $this->defaultColours != null) {
            $parts = explode(";", $this->defaultColours);
            if(count($parts) == 3 && Map::checkHex($parts[0])) {
                return $parts[0];
            }
        }
        return $this->getDefResetBackgroundColour();
    }

    public function getDefResetBackgroundColour() {
        return Map::RGBToHex(AbstractMapGenerator::$DEFAULT_BACKGROUND_COLOUR);
    }

    public function setDefaultColours($background, $player, $barbarian) {
        $defCol = ((Map::checkHex($background))?($background):(AbstractMapGenerator::$DEFAULT_BACKGROUND_COLOUR)) . ";";
        $defCol .= ((Map::checkHex($player))?($player):(AbstractMapGenerator::$DEFAULT_PLAYER_COLOUR)) . ";";
        $defCol .= ((Map::checkHex($barbarian))?($barbarian):(AbstractMapGenerator::$DEFAULT_BARBARIAN_COLOUR));
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
            return $this->barbarianEnabledDefault();
        }

        $parts = explode(";", $this->defaultColours);
        return $parts[2] != "null";
    }

    public function barbarianEnabledDefault() {
        return true;
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
            return $this->playerEnabledDefault();
        }

        $parts = explode(";", $this->defaultColours);
        return $parts[1] != "null";
    }

    public function playerEnabledDefault() {
        return true;
    }

    public function continentNumbersEnabled() {
        if(!isset($this->continentNumbers) || $this->continentNumbers == null) {
            return true;
        }

        return $this->continentNumbers == 1;
    }

    public function setDimensions($array) {
        $dim = "" . ((int) $array['xs']) . ";";
        $dim .= ((int) $array['xe']) . ";";
        $dim .= ((int) $array['ys']) . ";";
        $dim .= ((int) $array['ye']);
        $this->dimensions = $dim;
    }

    public function getDimensions() {
        if(!isset($this->dimensions) || $this->dimensions == null) {
            return AbstractMapGenerator::$DEFAULT_DIMENSIONS;
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
     * Configure given AbstractMapGenerator for rendering this map
     * @param \App\Util\AbstractMapGenerator $generator Generator to Configure
     */
    public function prepareRendering(AbstractMapGenerator $generator) {
        if(isset($this->markers) && $this->markers != null) {
            foreach(explode(";", $this->markers) as $marker) {
                $parts = explode(":", $marker);
                if(count($parts) < 3 || count($parts) > 4) continue;

                $rgb = Map::hexToRGB($parts[2]);
                $showText = count($parts) > 3 && strpos($parts[3], "t") !== false;
                $highlight = count($parts) > 3 && strpos($parts[3], "h") !== false;
                switch($parts[0]) {
                    case 'a':
                        $generator->markAlly($parts[1], $rgb, $showText, $highlight);
                        break;
                    case 'p':
                        $generator->markPlayer($parts[1], $rgb, $showText, $highlight);
                        break;
                    case 'v':
                        $generator->markVillage($parts[1], $rgb, $showText, $highlight);
                        break;
                }
            }
        }

        if(isset($this->defaultColours) && $this->defaultColours != null) {
            $parts = explode(";", $this->defaultColours);
            if(count($parts) == 3) {
                $rgb = Map::hexToRGB($parts[0]);
                if($rgb != null) {
                    $generator->setBackgroundColour($rgb);
                }

                $rgb = Map::hexToRGB($parts[1]);
                if($rgb != null) {
                    $generator->setPlayerColour($rgb);
                }
                if($parts[1] == "null") {
                    $generator->setPlayerColour(null);
                }

                $rgb = Map::hexToRGB($parts[2]);
                if($rgb != null) {
                    $generator->setBarbarianColour($rgb);
                }
                if($parts[2] == "null") {
                    $generator->setBarbarianColour(null);
                }
            }
        }
        
        if($this->autoDimensions) {
            $generator->setAutoResize(true);
        } else if(isset($this->dimensions) && $this->dimensions != null) {
            $generator->setAutoResize(false);
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

        if(isset($this->drawing_dim) && $this->drawing_dim != null && $this->drawing_dim != "" &&
                isset($this->drawing_png) && $this->drawing_png != null && $this->drawing_png != "" &&
                isset($this->drawing_obj) && $this->drawing_obj != null && $this->drawing_obj != "") {

            $parts = explode(";", $this->drawing_dim);
            if(count($parts) == 4) {
                $drawing_dim = [
                    'xs' => $parts[0],
                    'xe' => $parts[1],
                    'ys' => $parts[2],
                    'ye' => $parts[3],
                ];
            }
            $generator->setDrawings($this->drawing_png, $drawing_dim);
        }

        if(isset($this->markerFactor)) {
            $generator->setMarkerFactor($this->markerFactor);
        }
        $generator->setShowContinentNumbers($this->continentNumbersEnabled());

        $generator->setLayerOrder($this->getLayerConfiguration());
    }

    public function getLayerConfiguration() {
        $layers = [AbstractMapGenerator::$LAYER_MARK, AbstractMapGenerator::$LAYER_GRID, AbstractMapGenerator::$LAYER_TEXT];

        if(isset($this->drawing_dim) && $this->drawing_dim != null && $this->drawing_dim != "" &&
                isset($this->drawing_png) && $this->drawing_png != null && $this->drawing_png != "" &&
                isset($this->drawing_obj) && $this->drawing_obj != null && $this->drawing_obj != "") {
            $layers[] = AbstractMapGenerator::$LAYER_DRAWING;
        }
        return $layers;
    }
    
    public function makerFactorDefault() {
        return 0.2;
    }

    public static function checkHex($hex) {
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

    public static function hexToRGB($hex) {
        if(! Map::checkHex($hex)) return null;
        if(strlen($hex) != 6) return null;
        $hexChars = "0123456789ABCDEF";

        return [
            strpos($hexChars, $hex[0]) * 16 + strpos($hexChars, $hex[1]),
            strpos($hexChars, $hex[2]) * 16 + strpos($hexChars, $hex[3]),
            strpos($hexChars, $hex[4]) * 16 + strpos($hexChars, $hex[5]),
        ];
    }
    public static function RGBToHex($rgb) {
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
