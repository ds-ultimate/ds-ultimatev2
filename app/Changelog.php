<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Changelog extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'version',
        'title',
        'content',
        'repository_html_url',
        'icon',
        'color',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

}
