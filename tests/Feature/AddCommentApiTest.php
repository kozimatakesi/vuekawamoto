<?php

namespace Tests\Feature;

use App\Photo;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AddCommentApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        // テストユーザー作成
        $this->user = factory(User::class)->create();
    }

    /**
     * @test
     */
    public function should_コメントを追加できる()
    {
        factory(Photo::class)->create();
        $photo = Photo::first();

        $content = 'sample content';

        $response = $this->actingAs($this->user)
            ->json('POST', route('photo.comment', [
                'photo' => $photo->id,
            ]), compact('content'));

        $comments = $photo->comments()->get();

        $response->assertStatus(201)
            // JSONフォーマットが期待通りであること
            ->assertJsonFragment([
                "author" => [
                    "follow_by_user" => false,
                    "id" => $this->user->id,
                    "name" => $this->user->name,
                ],
                "content" => $content
            ]);

        // DBにコメントが1件登録されていること
        $this->assertEquals(1, $comments->count());
        // 内容がAPIでリクエストしたものであること
        $this->assertEquals($content, $comments[0]->content);
    }

    /**
     * @test
     */
    public function should_バリデーションコメントの表示_未入力()
    {
      factory(Photo::class)->create();
      $photo = Photo::first();

      $content = '';

      $response = $this->actingAs($this->user)
      ->json('POST', route('photo.comment', [
          'photo' => $photo->id,
      ]), compact('content'));

      $response->assertJsonValidationErrors([
        "content" => "content は入力してください。"  ,
      ]);
    }

        /**
     * @test
     */
    public function should_バリデーションコメントの表示_51文字以上()
    {
      factory(Photo::class)->create();
      $photo = Photo::first();

      $content = 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';

      $response = $this->actingAs($this->user)
      ->json('POST', route('photo.comment', [
          'photo' => $photo->id,
      ]), compact('content'));

      $response->assertJsonValidationErrors([
        "content" => "content は 50 文字以内にしてください。" ,
      ]);
    }

}
