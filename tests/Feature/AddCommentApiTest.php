<?php

namespace Tests\Feature;

use App\Models\Photo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AddCommentApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }

    /**
     * コメント投稿
     * @group comment
     */
    public function testComment()
    {
        factory(Photo::class)->create();
        $photo = Photo::first();

        $comment = 'sample body';

        $response = $this->actingAs($this->user)
            ->json('POST', route('photos.comment', [
                'photo' => $photo->id,
            ]), compact('comment'));

        $comments = $photo->comments()->get();

        $response->assertStatus(201)
            ->assertJsonFragment([
                "author" => [
                    "name" => $this->user->name,
                ],
                "body" => $comment,
            ]);

        $this->assertEquals(1, $comments->count());
        $this->assertEquals($comment, $comments[0]->body);
    }
}
