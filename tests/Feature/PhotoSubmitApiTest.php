<?php

namespace Tests\Feature;

use App\Models\Photo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PhotoSubmitApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }

    /**
     * ファイルをアップロードできるか
     * @group file
     */
    public function testUploadFile()
    {
        Storage::fake('s3');

        $response = $this->actingAs($this->user)
            ->json('POST', route('photos.store'), [
                'photo' => UploadedFile::fake()->image('photo.png'),
            ]);

        $response->assertStatus(200);

        $photo = Photo::first();

        // 写真のIDが12桁のランダムな文字列であること
        $this->assertRegExp('/^[0-9a-zA-Z-_]{12}$/', $photo->id);

        // DBに挿入されたファイル名のファイルがストレージに保存されていること
        Storage::disk('s3')->assertExists($photo->filename);
    }

    /**
     * DBエラーの場合はファイルを保存しない
     * @group file
     */
    public function testNotCreateIfDBError()
    {
        // DBエラーを起こす
        Schema::drop('photos');

        Storage::fake('s3');

        $response = $this->actingAs($this->user)
            ->json('POST', route('photos.store'), [
                'photo' => UploadedFile::fake()->image('photo.jpg'),
            ]);

        $response->assertStatus(500);

        // ストレージにファイルが保存されていないこと
        $this->assertEquals(0, count(Storage::cloud()->files()));
    }

    /**
     * ファイル保存エラーの場合はDBにレコード追加しない
     */
    public function testNotInsertRedordIfFileUploadError()
    {
        // ストレージをモックして保存時にエラーを起こさせる
        Storage::shouldReceive('cloud')
            ->once()
            ->andReturnNull();

        $response = $this->actingAs($this->user)
            ->json('POST', route('photos.store'), [
                'photo' => UploadedFile::fake()->image('photo.jpg'),
            ]);

        $response->assertStatus(500);

        // DBに何も挿入されていないこと
        $this->assertEmpty(Photo::all());
    }
}
