<?php

declare(strict_types=1);

namespace Kelnik\Progress\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Kelnik\Progress\Database\Factories\AlbumVideoFactory;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property int $album_id
 * @property int $priority
 * @property string $url
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property Album $album
 */
final class AlbumVideo extends Model
{
    use AsSource;
    use HasFactory;

    public const PRIORITY_DEFAULT = 500;
    public const DEFAULT_INT_VALUE = 0;

    protected $table = 'progress_album_videos';

    protected $attributes = [
        'album_id' => self::DEFAULT_INT_VALUE,
        'priority' => self::PRIORITY_DEFAULT
    ];

    protected $fillable = [
        'priority', 'url'
    ];

    public ?string $coverImage = null;

    protected static function newFactory(): AlbumVideoFactory
    {
        return AlbumVideoFactory::new();
    }

    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class, 'album_id')->withDefault();
    }
}
