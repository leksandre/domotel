<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Kelnik\EstateVisual\Models\Casts\PositionCast;
use Kelnik\EstateVisual\Models\Contracts\EstateVisualModel;
use Kelnik\EstateVisual\Models\Enums\PointerType;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property int $angle_id
 * @property PointerType $type
 * @property Contracts\Position $position
 * @property array $data
 *
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property StepElementAngle $angle
 */
final class StepElementAnglePointer extends EstateVisualModel
{
    use AsSource;
    use HasFactory;

    protected $table = 'estate_visual_step_element_angle_pointers';

    protected $attributes = [
        'angle_id' => 0,
        'position' => '[0,0]',
        'data' => '[]'
    ];

    protected $fillable = [
        'type',
        'position',
        'data'
    ];

    protected $casts = [
        'type' => PointerType::class,
        'position' => PositionCast::class,
        'data' => 'array'
    ];

    public function __construct(array $attributes = [])
    {
        if (!isset($attributes['type'])) {
            $attributes['type'] = PointerType::Text;
        }

        parent::__construct($attributes);
    }

    public function angle(): BelongsTo
    {
        return $this->belongsTo(StepElementAngle::class, 'angle_id')->withDefault();
    }
}
