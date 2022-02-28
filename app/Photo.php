<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Photo extends Model
{
    protected $keyType = 'string';
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
}
