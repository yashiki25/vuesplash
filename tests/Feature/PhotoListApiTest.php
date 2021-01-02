<?php

namespace Tests\Feature;

use App\Models\Photo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PhotoListApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 写真一覧JSONを正しい構造で受け取る
     * @group photo
     */
    public function testFetchPhotos()
    {
        factory(Photo::class, 5)->create();

        $response = $this->json('GET', route('photos.index'));

        $photos = Photo::with(['owner'])
            ->orderBy('created_at', 'desc')
            ->get();

        $expectedData = $photos->map(function ($photo) {
            return [
                'id' => $photo->id,
                'url' => $photo->url,
                'owner' => [
                    'name' => $photo->owner->name,
                ],
                'liked_by_user' => false,
                'likes_count' => 0,
            ];
        })->all();

        // レスポンスJSONのdata項目が期待値と合致すること
        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonFragment([
                "data" => $expectedData,
            ]);
    }
}
