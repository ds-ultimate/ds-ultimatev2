<?php

namespace App;

use App\Tool\Map\Map;
use App\Util\AbstractMapGenerator;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $dates = [
        'updated_at',
        'created_at',
        'last_seen_changelog',
    ];

    protected $fillable = [
        'user_id',
        'birthday',
        'show_birthday',
        'skype',
        'show_skype',
        'discord_id',
        'show_discord',
        'github_id',
        'facebook_id',
        'google_id',
        'twitter_id',
        'last_seen_changelog',
        'created_at',
        'updated_at',

        'map_dimensions',
        'map_defaultColours',
        'map_markerFactor',

        'conquerHightlight_World',
        'conquerHightlight_Ally',
        'conquerHightlight_Player',
        'conquerHightlight_Village',
    ];

    public function checkOauth($driver){
        $name = $driver.'_id';
        return isset($this->$name);
    }


    /*
     * Functions for map settings
     */

    public function getDefPlayerColour() {
        if(isset($this->map_defaultColours) && $this->map_defaultColours != null) {
            $parts = explode(";", $this->map_defaultColours);
            if(count($parts) == 3 && Map::checkHex($parts[1])) {
                return $parts[1];
            }
        }
        return Map::RGBToHex(AbstractMapGenerator::$DEFAULT_PLAYER_COLOUR);
    }

    public function getDefBarbarianColour() {
        if(isset($this->map_defaultColours) && $this->map_defaultColours != null) {
            $parts = explode(";", $this->map_defaultColours);
            if(count($parts) == 3 && Map::checkHex($parts[2])) {
                return $parts[2];
            }
        }
        return Map::RGBToHex(AbstractMapGenerator::$DEFAULT_BARBARIAN_COLOUR);
    }

    public function getBackgroundColour() {
        if(isset($this->map_defaultColours) && $this->map_defaultColours != null) {
            $parts = explode(";", $this->map_defaultColours);
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
        $this->map_defaultColours = $defCol;
    }

    public function disableBarbarian() {
        if(!isset($this->map_defaultColours) || $this->map_defaultColours == null) {
            return;
        }

        $parts = explode(";", $this->map_defaultColours);
        $this->map_defaultColours = "{$parts[0]};{$parts[1]};null";
    }

    public function barbarianEnabled() {
        if(!isset($this->map_defaultColours) || $this->map_defaultColours == null) {
            return true;
        }

        $parts = explode(";", $this->map_defaultColours);
        return $parts[2] != "null";
    }

    public function disablePlayer() {
        if(!isset($this->map_defaultColours) || $this->map_defaultColours == null) {
            return;
        }

        $parts = explode(";", $this->map_defaultColours);
        $this->map_defaultColours = "{$parts[0]};null;{$parts[2]}";
    }

    public function playerEnabled() {
        if(!isset($this->map_defaultColours) || $this->map_defaultColours == null) {
            return true;
        }

        $parts = explode(";", $this->map_defaultColours);
        return $parts[1] != "null";
    }

    public function setDimensions($array) {
        $dim = "" . ((int) $array['xs']) . ";";
        $dim .= ((int) $array['xe']) . ";";
        $dim .= ((int) $array['ys']) . ";";
        $dim .= ((int) $array['ye']);
        $this->map_dimensions = $dim;
    }

    public function getDimensions() {
        if(!isset($this->map_dimensions) || $this->map_dimensions == null) {
            return AbstractMapGenerator::$DEFAULT_DIMENSIONS;
        }
        $parts = explode(";", $this->map_dimensions);
        return [
            'xs' => (int) $parts[0],
            'xe' => (int) $parts[1],
            'ys' => (int) $parts[2],
            'ye' => (int) $parts[3],
        ];
    }

    public static $CONQUER_HIGHLIGHT_MAPPING = [
        's' => 'self', 'i' => 'internal', 'b' => 'barbarian', 'd' => 'deleted',
        'w' => 'win', 'l' => 'loose',
    ];
}
