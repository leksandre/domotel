<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Kelnik\EstateVisual\Models\Contracts\EstateVisualModel;
use Kelnik\EstateVisual\Models\Traits\ScopeAdminList;
use Orchid\Attachment\Models\Attachment;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property int $element_id
 * @property int $image_id
 * @property int $degree
 * @property array $shift
 * @property string $title
 *
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property StepElement $element
 * @property Attachment $render
 * @property Collection $masks
 * @property Collection $pointers
 *
 * @method Builder adminList()
 */
final class StepElementAngle extends EstateVisualModel
{
    use AsSource;
    use HasFactory;
    use ScopeAdminList;

    protected $table = 'estate_visual_step_element_angles';

    protected $casts = [
        'shift' => 'array'
    ];

    protected $attributes = [
        'element_id' => 0,
        'degree' => 0,
        'shift' => '[0,0]',
    ];

    protected $fillable = [
        'degree', 'shift', 'title', 'image_id'
    ];

    protected array $attachmentAttributes = ['image_id'];

    protected static function boot(): void
    {
        parent::boot();

        self::deleted(function (self $angle) {
            $angle->masks()->delete();
            $angle->pointers()->delete();
        });
    }

    public function element(): BelongsTo
    {
        return $this->belongsTo(StepElement::class, 'element_id')->withDefault();
    }

    public function render(): BelongsTo
    {
        return $this->belongsTo(Attachment::class, 'image_id')->withDefault();
    }

    public function masks(): HasMany
    {
        return $this->hasMany(StepElementAngleMask::class, 'angle_id');
    }

    public function pointers(): HasMany
    {
        return $this->hasMany(StepElementAnglePointer::class, 'angle_id');
    }
}
