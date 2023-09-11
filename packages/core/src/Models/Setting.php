<?php

declare(strict_types=1);

namespace Kelnik\Core\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Kelnik\Core\Database\Factories\SettingFactory;

/**
 * @property string $module
 * @property string $name
 * @property Collection $value
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
final class Setting extends Model
{
    use HasFactory;

    protected $table = 'settings';

    protected $primaryKey = false;

    public $incrementing = false;

    protected $casts = [
        'value' => 'collection'
    ];

    protected $fillable = [
        'module',
        'name',
        'value'
    ];

    protected static function newFactory(): SettingFactory
    {
        return SettingFactory::new();
    }
}
