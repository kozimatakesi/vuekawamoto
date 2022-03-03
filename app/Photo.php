<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Arr;

class Photo extends Model
{
    protected $perPage = 9;
    protected $keyType = 'string';

    /** JSONに含めるアクセサ */
    protected $appends = [
        'url', 'likes_count', 'liked_by_user',
    ];

    /** JSONに含める属性 */
    protected $visible = [
        'id', 'owner', 'url','original_filename','comments',
        'likes_count', 'liked_by_user',
    ];

    // IDの長さ
    const ID_LENGTH = 12;

    public function __construct(array $attributes = [])
    {
        // 親モデル Modelのコンストラクタを持ってきてる
        parent::__construct($attributes);

        // attributesプロパティにidがなければ、実行する
        if(! Arr::get($this->attributes, 'id')) {
            $this->setId();
        }
    }

    private function setId()
    {
        $this->attributes['id'] = $this->getRandomId();
    }

    private function getRandomId()
    {
        $characters = array_merge(
            range(0, 9), range('a', 'z'),
            range('A', 'Z'), ['-', '_']
        );

        $length = count($characters);

        $id = "";

        for ($i = 0; $i < self::ID_LENGTH; $i++) {
            $id .= $characters[random_int(0, $length - 1)];
        }

        return $id;
    }

    /**
     * リレーションシップ - usersテーブル
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo('App\User', 'user_id', 'id', 'users');
    }

    /**
     * アクセサ - url
     * @return string
     */
    public function getUrlAttribute()
    {
        return Storage::cloud()->url($this->attributes['filename']);
    }

    /**
     * アクセサ - likes_count
     * @return int
     */
    public function getLikesCountAttribute()
    {
        return $this->likes->count();
    }

    /**
     * アクセサ - liked_by_user
     * @return boolean
     */
    public function getLikedByUserAttribute()
    {
        if (Auth::guest()) {
            return false;
        }

        return $this->likes->contains(function ($user) {
            return $user->id === Auth::user()->id;
        });
    }

    /**
     * リレーションシップ - commentsテーブル
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany('App\Comment')->orderBy('id', 'desc');
    }

    /**
     * リレーションシップ - usersテーブル
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function likes()
    {
        return $this->belongsToMany('App\User', 'likes')->withTimestamps();
    }

}
