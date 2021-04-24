<?php

namespace App\Tool\AnimHistMap;

use App\AllyTop;
use App\HistoryIndex;
use App\PlayerTop;
use App\Village;
use App\World;
use App\Tool\Map\Map;
use App\Util\AbstractMapGenerator;
use App\Util\BasicFunctions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnimHistMapMap extends Model
{
    use SoftDeletes;

    protected $table = 'animHistMapMap';
    protected $connection = 'mysql';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $hidden = [
        'edit_key',
        'show_key',
    ];

    protected $fillable = [
        'world_id',
        'user_id',
        'edit_key',
        'show_key',
        'markers',
        'opaque',
        'skin',
        'layers',
        'autoDimensions',
        'dimensions',
        'defaultColours',
        'title',
        'markerFactor',
        'continentNumbers',
        'showLegend',
        'legendSize',
        'legendPosition',
    ];

    public static $copyToJob = [
        'world_id',
        'user_id',
        'edit_key',
        'show_key',
        'markers',
        'opaque',
        'skin',
        'layers',
        'autoDimensions',
        'dimensions',
        'defaultColours',
        'title',
        'markerFactor',
        'continentNumbers',
        'showLegend',
        'legendSize',
        'legendPosition',
    ];

    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }

    public function world(){
        return $this->belongsTo('App\World', 'world_id');
    }

    public function getTitle() {
        if($this->title == null || $this->title == "") {
            return ucfirst(__('tool.animHistMap.title'));
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
                    $ally = AllyTop::ally($world->server->code, $world->name, $parts[1]);
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
                    $player = PlayerTop::player($world->server->code, $world->name, $parts[1]);
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
        return Map::RGBToHex(AbstractMapGenerator::$DEFAULT_PLAYER_COLOUR);
    }

    public function getDefBarbarianColour() {
        if(isset($this->defaultColours) && $this->defaultColours != null) {
            $parts = explode(";", $this->defaultColours);
            if(count($parts) == 3 && Map::checkHex($parts[2])) {
                return $parts[2];
            }
        }
        return Map::RGBToHex(AbstractMapGenerator::$DEFAULT_BARBARIAN_COLOUR);
    }

    public function getBackgroundColour() {
        if(isset($this->defaultColours) && $this->defaultColours != null) {
            $parts = explode(";", $this->defaultColours);
            if(count($parts) == 3 && Map::checkHex($parts[0])) {
                return $parts[0];
            }
        }
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

        if(isset($this->markerFactor)) {
            $generator->setMarkerFactor($this->markerFactor);
        }
        $generator->setShowContinentNumbers($this->continentNumbersEnabled());

        $generator->setLayerOrder($this->getLayerConfiguration());
    }

    public function getLayerConfiguration() {
        return [AbstractMapGenerator::$LAYER_MARK, AbstractMapGenerator::$LAYER_GRID, AbstractMapGenerator::$LAYER_TEXT];
    }
    
    /**
     * returns preview route for this map
     */
    public function preview() {
        $dbName = BasicFunctions::getDatabaseName($this->world->server->code, $this->world->name);
        $histIdx = (new HistoryIndex())->setTable("$dbName.index")->orderBy("id", "desc")->first();
        return route('tools.animHistMap.preview', [$this->id, $this->show_key, $histIdx->id, 'png']);
    }
}
