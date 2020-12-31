<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }

    /**
     * ログイン中のユーザーを返却する
     * @group getUser
     */
    public function testReturnAuthUser()
    {
        $response = $this->actingAs($this->user)->json('GET', route('user'));

        $response->assertStatus(200);
    }

    /**
     * 未ログインの場合、空文字を返却する
     * @group getUser
     */
    public function testReturnEmptyCharacterStringIfNotLoggedIn()
    {
        $response = $this->json('GET', route('user'));

        $response->assertStatus(200);
        $this->assertEquals('', $response->content());
    }

}
