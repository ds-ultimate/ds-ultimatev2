<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomModel extends Model
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
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
}
