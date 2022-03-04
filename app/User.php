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
        // follower_userテーブルからそのユーザーインスタンスのidがuser_idと一致するレコードを返す
        // そのuser_idと一致したレコードのfollower_idとuserテーブルのidと等しいレコードを返す
        return $this->belongsToMany(self::class, 'follower_user', 'follower_id', 'user_id');
    }

    public function followers()
    {
        // belongsToManyの場合は第2引数に従テーブル名,第3引数に従テーブルの外部キー,第4引数に従テーブルの外部キー
        // follower_userテーブルからそのユーザーインスタンスのidがfollower_idと一致するものを返す
        return $this->belongsToMany(self::class, 'follower_user', 'user_id', 'follower_id');
    }
}
