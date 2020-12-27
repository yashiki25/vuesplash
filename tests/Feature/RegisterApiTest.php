<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 新規ユーザー登録
     * @group auth
     */
    public function testRegisterUser()
    {
        $data = [
            'name' => 'test user',
            'email' => 'test@example.com',
            'password' => 'test1234',
            'password_confirmation' => 'test1234',
        ];

        $response = $this->json('POST', route('register'), $data);

        $user = User::first();
        self::assertEquals($data['name'], $user->name);

        $response->assertStatus(201)
            ->assertJson(['name' => $user->name]);
    }

}
