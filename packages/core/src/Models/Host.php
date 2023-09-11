<?php

declare(strict_types=1);

namespace Kelnik\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Kelnik\Core\Database\Factories\HostFactory;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property int $site_id
 * @property string $value
 *
 * @property Site $site
 */
final class Host extends Model
{
    use AsSource;
    use HasFactory;

    protected $table = 'site_hosts';

    protected $fillable = [
        'value'
    ];

    protected static function newFactory(): HostFactory
    {
        return HostFactory::new();
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'site_id', 'id')->withDefault();
    }
}
