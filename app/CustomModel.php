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

    /**
     * Get an attribute from the model.
     * Copied from Illuminate\Database\Eloquent\Concerns\HasAttributes
     * Special cas for fake eager loading using custom joined queries
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        if (! $key) {
            return;
        }

        // If the attribute exists in the attribute array or has a "get" mutator we will
        // get the attribute's value. Otherwise, we will proceed as if the developers
        // are asking for a relationship's value. This covers both types of values.
        if (array_key_exists($key, $this->attributes) ||
            array_key_exists($key, $this->casts) ||
            $this->hasGetMutator($key) ||
            $this->hasAttributeMutator($key) ||
            $this->isClassCastable($key)) {
            return $this->getAttributeValue($key);
        }

        $relPos = strpos($key, "__");
        if($relPos !== false) {
            $mKey = substr($key, 0, $relPos);
            $parm = substr($key, $relPos + 2);

            $rMod = $this->$mKey;
            if($rMod === null) {
                return null;
            }
            return $rMod->$parm;
        }

        // Here we will determine if the model base class itself contains this given key
        // since we don't want to treat any of those methods as relationships because
        // they are all intended as helper methods and none of these are relations.
        if (method_exists(self::class, $key)) {
            return $this->throwMissingAttributeExceptionIfApplicable($key);
        }

        return $this->isRelation($key) || $this->relationLoaded($key)
                    ? $this->getRelationValue($key)
                    : $this->throwMissingAttributeExceptionIfApplicable($key);
    }
}
