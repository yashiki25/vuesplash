<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\StorePhotoRequest;
use App\Models\Comment;
use App\Models\Photo;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $photos = Photo::with(['owner'])
            ->orderBy(Photo::CREATED_AT, 'desc')
            ->paginate();

        return response($photos, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StorePhotoRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePhotoRequest $request)
    {
        $extension = $request->photo->extension();

        $photo = new Photo();
        // インスタンス生成時に作成したランダムIDと拡張子を結合
        $photo->filename = $photo->id . '.' . $extension;

        Storage::cloud()->putFileAs('', $request->photo, $photo->filename, 'public');

        DB::transaction(function () use ($photo) {
            try {
                Auth::user()->photos()->save($photo);
            } catch (Exception $e) {
                Storage::disk('s3')->delete($photo->filename);
                throw $e;
            }
        });

        return response($photo, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param string $photoId
     * @return \Illuminate\Http\Response
     */
    public function show(string $photoId)
    {
        $photo = Photo::with(['owner', 'comments.author'])
            ->where('id', $photoId)
            ->firstOrFail();

        return response($photo, 200);
    }

    /**
     * 写真ダウンロード
     * @param Photo $photo
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function download(Photo $photo)
    {
        // 写真の存在チェック
        if (!Storage::cloud()->exists($photo->filename)) {
            abort(404);
        }

        $disposition = "attachment; filename={$photo->filename}";
        $headers = [
            'Content-Type' => 'application/octet-stream',
            // https://developer.mozilla.org/ja/docs/Web/HTTP/Headers/Content-Disposition
            'Content-Disposition' => $disposition,
        ];

        return response(Storage::cloud()->get($photo->filename), 200, $headers);
    }

    /**
     * コメント投稿
     * @param Photo $photo
     * @param StoreCommentRequest $request
     * @return \Illuminate\Http\Response
     */
    public function addComment(Photo $photo, StoreCommentRequest $request)
    {
        $comment = new Comment();
        $comment->body = $request->get('comment');
        $comment->user_id = Auth::user()->id;
        $photo->comments()->save($comment);

        $newComment = Comment::with('author')
            ->where('id', $comment->id)
            ->firstOrFail();

        return response($newComment, 201);
    }

    /**
     * いいね
     * @param string $photoId
     * @return \Illuminate\Http\Response
     */
    public function like(string $photoId)
    {
        $photo = Photo::with('likes')
            ->where('id', $photoId)
            ->firstOrFail();

        $photo->likes()->detach(Auth::user()->id);
        $photo->likes()->attach(Auth::user()->id);

        return response(['photo_id' => $photoId], 200);
    }

    /**
     * いいね解除
     * @param string $photoId
     * @return \Illuminate\Http\Response
     */
    public function unlike(string $photoId)
    {
        $photo = Photo::with('likes')
            ->where('id', $photoId)
            ->firstOrFail();

        $photo->likes()->detach(Auth::user()->id);

        return response(['photo_id' => $photoId], 200);
    }
}
