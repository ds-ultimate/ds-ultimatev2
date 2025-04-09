<?php

namespace App;

use App\Http\Controllers\User\SettingsController;
use Hash;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements MustVerifyEmail
{
    use SoftDeletes, Notifiable;

    public $table = 'users';

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'last_seen' => 'datetime',
        'updated_at' => 'datetime',
        'created_at' => 'datetime',
        'deleted_at' => 'datetime',
        'email_verified_at' => 'datetime',
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'last_seen',
        'remember_token',
        'email_verified_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * @param $input
     */
    public function setPasswordAttribute($input)
    {
        if ($input) {
            $this->attributes['password'] = app('hash')->needsRehash($input) ? Hash::make($input) : $input;
        }
    }

    /**
     * @param string $token
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany('\App\Role');
    }

    /**
     * Route notifications for the mail channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return string
     */
    public function routeNotificationForMail($notification)
    {
        return $this->email;
    }

    public function BugreportComments()
    {
        return $this->hasMany('App\BugreportComment');
    }

    public static function boot()
    {
        parent::boot();

        static::created(function ($user){
            $profile = new Profile();
            $profile->user_id = $user->id;
            $profile->save();
        });
    }

    public function followAttackList()
    {
        return $this->morphedByMany('App\Tool\AttackPlanner\AttackList', 'followable', 'follows');
    }

    public function followMap()
    {
        return $this->morphedByMany('App\Tool\Map\Map', 'followable', 'follows');
    }

    public function followPlayer()
    {
        return $this->morphedByMany('App\Player', 'followable', 'follows');
    }

    public function profile()
    {
        return $this->hasOne('App\Profile');
    }

    public function dsConnection()
    {
        return $this->hasMany('App\DsConnection');
    }

    public function routeNotificationForDiscord()
    {
        return $this->profile->discord_private_channel_id;
    }
}
