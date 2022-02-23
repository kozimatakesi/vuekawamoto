<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginApiTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }

    /**
     * @test
     */


    public function should_登録済のユーザーを認証して返却する()
    {
        $data = [
            'email' => $this->user->email,
            'password' => 'password'
        ];

        $response = $this->json('POST', route('login'), $data);

        $response
            ->assertStatus(200)
            ->assertJson(['name' => $this->user->name]);
        $this->assertAuthenticatedAs($this->user);
    }
}
