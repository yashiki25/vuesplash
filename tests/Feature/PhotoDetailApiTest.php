<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Photo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PhotoDetailApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 写真詳細JSONを正しい構造で受け取る
     * @group fetchPhotoDetail
     */
    public function testFetchPhoto()
    {
        factory(Photo::class)->create()->each(function ($photo) {
            $photo->comments()->saveMany(factory(Comment::class, 3)->make());
        });

        $photo = Photo::first();

        $response = $this->json('GET', route('photos.show', [
            'id' => $photo->id,
        ]));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $photo->id,
                'url' => $photo->url,
                'owner' => [
                    'name' => $photo->owner->name,
                ],
                'comments' => $photo->comments
                    ->sortByDesc('id')
                    ->map(function ($comment) {
                        return [
                            'author' => [
                                'name' => $comment->author->name,
                            ],
                            'body' => $comment->body,
                        ];
                    })->all(),
            ]);
    }
}
