<?php

namespace App;

use App\Util\BasicFunctions;
use Illuminate\Database\Eloquent\Model;

/**
 * Adapted has many for special Database structure
 * Supports caching
 */
class CustomModel extends Model
{
    protected $cache = [];
    
    public function __construct($arg1 = [], $arg2 = null)
    {
        if($arg1 instanceof World) {
            //call with world + tableName
            parent::__construct();
            $this->setTable(BasicFunctions::getWorldDataTable($arg1, $arg2));
        } else {
            //called with attributeArray
            parent::__construct($arg1);
        }
    }

    public function myhasMany($related, $foreignKey, $localKey, $table)
    {
        $instance = $this->newRelatedInstance($related);

        $instance->setTable($table);

        return $this->newHasMany(
            $instance->newQuery(), $this, $instance->getTable().'.'.$foreignKey, $localKey
        );
    }

    /*
     * Angepasste Funktion für die Ablageart der DB
     * */
    public function mybelongsTo($related, $foreignKey, $ownerKey, $table, $relation = null)
    {
        if (is_null($relation)) {
            $relation = $this->guessBelongsToRelation();
        }

        $instance = $this->newRelatedInstance($related);

        if (is_null($foreignKey)) {
            $foreignKey = Str::snake($relation).'_'.$instance->getKeyName();
        }
        
        $instance->setTable($table);

        return $this->newBelongsTo(
            $instance->newQuery(), $this, $foreignKey, $ownerKey, $relation
        );
    }
    
    private static $cacheData = [];
    public function getRelationValue($key)
    {
        if(in_array($key, $this->cache)) {
            $rel = $this->$key();
            $foreignID = $this->{$rel->getForeignKeyName()};
            $foreignCls = get_class($rel->getQuery()->getModel());
            
            //we want to use the cached version of that
            if(! isset(static::$cacheData[$foreignCls])) {
                static::$cacheData[$foreignCls] = [];
            } else if(isset(static::$cacheData[$foreignCls][$foreignID])) {
                return static::$cacheData[$foreignCls][$foreignID];
            }
            $val = $rel->getResults();
            static::$cacheData[$foreignCls][$foreignID] = $val;
            return $val;
        }
        return parent::getRelationValue($key);
    }
    
    public function getRelativeTable($table) {
        $tableNames = [
            "ally_",
            "player_",
            "village_",
            "conquer",
        ];
        foreach($tableNames as $tblName) {
            $p = strrpos($this->table, $tblName);
            if($p !== false) {
                return substr($this->table, 0, $p) . $table;
            }
        }
    }
}
