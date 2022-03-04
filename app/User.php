<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use Notifiable;

    /** JSONに含めるアクセサ */
    protected $appends = [
      'follow_by_user',
    ];

    protected $visible = [
        'id','name','follow_by_user'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    /**
     * リレーションシップ - photosテーブル
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function photos()
    {
        return $this->hasMany('App\Photo');
    }

    /**
     * アクセサ follow_by_user
     * @return boolean
     */

    public function getFollowByUserAttribute()
    {
      if(Auth::guest()) {
        return false;
      }

      return $this->follows->contains(function ($user) {
        return $user->id === Auth::user()->id;
      });
    }

    /**
     * Relationships
     */
    public function follows()
    {
        return $this->belongsToMany(self::class, 'follower_user', 'follower_id', 'user_id');
    }

    public function followers()
    {
        return $this->belongsToMany(self::class, 'follower_user', 'user_id', 'follower_id');
    }
}
