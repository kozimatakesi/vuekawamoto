<?php

namespace Tests\Feature;

use App\Photo;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PhotoListApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();

    }

    /**
     * @test
     */
    public function should_正しい構造のJSONを返却する()
    {
        // 5つの写真データを生成する
        factory(Photo::class, 5)->create();

        $response = $this->json('GET', route('photo.index'));

        // 生成した写真データを作成日降順で取得
        $photos = Photo::with(['owner'])->orderBy('created_at', 'desc')->get();

        // data項目の期待値
        $expected_data = $photos->map(function ($photo) {
            return [
                'id' => $photo->id,
                'url' => $photo->url,
                'original_filename' => $photo->original_filename,
                'owner' => [
                    'follow_by_user' => false,
                    'id' => $photo->owner->id,
                    'name' => $photo->owner->name,
                ],
                'liked_by_user' => false,
                'likes_count' => 0,
            ];
        })
        ->all();

        $response->assertStatus(200)
            // レスポンスJSONのdata項目に含まれる要素が5つであること
            ->assertJsonCount(5, 'data')
            // レスポンスJSONのdata項目が期待値と合致すること
            ->assertJsonFragment([
                "data" => $expected_data,
            ]);
    }

    /**
    * @test
    */
    public function should_正しい構造のJSONを返却する_owner()
    {
        // 5つの写真データを生成する
        factory(Photo::class, 5)->create();

        $response = $this->actingAs($this->user)
                    ->json('GET', route('photo.own'));

        // 生成した写真データを作成日降順で取得
        $photos = Photo::with(['owner'])->orderBy('created_at', 'desc')->get();

        // data項目の期待値
        $expected_data = $photos->map(function ($photo) {
            return [
                'id' => $photo->id,
                'url' => $photo->url,
                'original_filename' => $photo->original_filename,
                'owner' => [
                    'follow_by_user' => false,
                    'id' => $photo->owner->id,
                    'name' => $photo->owner->name,
                ],
                'liked_by_user' => false,
                'likes_count' => 0,
            ];
        })
        ->all();


        $count = 0;
        $equal_user_array = array();

        foreach($expected_data as $data) {
          if($data['owner']['name'] === $this->user->name) {
            $count++;
            $equal_user_array[] = $data;
          }
        }

        $response->assertStatus(200)
            // レスポンスJSONのdata項目に含まれる要素が5つであること
            ->assertJsonCount($count, 'data');
            // レスポンスJSONのdata項目が期待値と合致すること
            // ->assertJsonFragment([
            //     "data" => $expected_data,
            // ]);
    }

}
