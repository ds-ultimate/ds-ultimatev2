<?php
/**
 * Created by IntelliJ IDEA.
 * User: crams
 * Date: 18.08.2019
 * Time: 16:10
 */

namespace App\Tool\AttackPlanner;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttackListItem extends Model
{
    use SoftDeletes;

    protected $fillable = [

    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
