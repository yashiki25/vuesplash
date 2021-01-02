<?php

namespace Tests\Feature;

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
        factory(Photo::class)->create();
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
            ]);
    }
}
