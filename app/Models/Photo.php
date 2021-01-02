<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class Photo extends Model
{
    protected $table = 'photos';

    protected $keyType = 'string';

    /** IDの桁数 */
    const ID_LENGTH = 12;

    protected $guarded = [
        'created_at',
        'updated_at',
    ];

    protected $dates = [
        'updated_at',
        'created_at',
    ];

    /** JSONに含めるユーザー定義アクセサ */
    protected $appends = [
        'url',
    ];

    /** JSONに含める属性 */
    protected $visible = [
        'id',
        'owner',
        'url',
    ];

    /**
     * 投稿者
     * @return BelongsTo
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id', 'users');
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if (! Arr::get($this->attributes, 'id')) {
            $this->setId();
        }
    }

    /**
     * ランダムなID値をid属性に代入する
     */
    private function setId()
    {
        $this->attributes['id'] = $this->getRandomId();
    }

    /**
     * ランダムなID値を生成する
     * @return string
     * @throws \Exception
     */
    private function getRandomId()
    {
        $characters = array_merge(
            range(0, 9), range('a', 'z'),
            range('A', 'Z'), ['-', '_']
        );

        $length = count($characters);

        $id = '';
        for ($i = 0; $i < self::ID_LENGTH; $i++) {
            $id .= $characters[random_int(0, $length - 1)];
        }

        return $id;
    }

    /**
     * ファイルURLを取得
     * @return string
     */
    public function getUrlAttribute(): string
    {
        return Storage::cloud()->url($this->attributes['filename']);
    }

}
