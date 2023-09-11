<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Kelnik\Estate\Models\Completion;
use Kelnik\Estate\Models\Premises;
use Kelnik\EstateVisual\Models\Casts\PositionCast;
use Kelnik\EstateVisual\Models\Contracts\EstateVisualModel;
use Kelnik\EstateVisual\Models\Enums\MaskType;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property int $angle_id
 * @property int $element_id
 * @property int $estate_premises_id
 * @property MaskType $type
 * @property string $value
 * @property Contracts\Position $pointer
 * @property string $coords
 *
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property StepElementAngle $angle
 * @property StepElement $element
 * @property Premises $premises
 */
final class StepElementAngleMask extends EstateVisualModel
{
    use AsSource;
    use HasFactory;

    protected $table = 'estate_visual_step_element_angle_masks';

    protected $attributes = [
        'angle_id' => 0,
        'element_id' => 0,
        'estate_premises_id' => 0,
        'pointer' => '[0,0]'
    ];

    protected $fillable = [
        'type', 'value', 'pointer', 'coords'
    ];

    protected $casts = [
        'pointer' => PositionCast::class,
        'type' => MaskType::class
    ];

    public array $premisesStat = [];
    public ?Completion $completion = null;

    public function __construct(array $attributes = [])
    {
        if (!isset($attributes['type'])) {
            $attributes['type'] = MaskType::Empty->value;
        }

        parent::__construct($attributes);
    }

    public function angle(): BelongsTo
    {
        return $this->belongsTo(StepElementAngle::class, 'angle_id')->withDefault();
    }

    public function element(): BelongsTo
    {
        return $this->belongsTo(StepElement::class, 'element_id')->withDefault();
    }

    public function premises(): BelongsTo
    {
        return $this->belongsTo(Premises::class, 'estate_premises_id')->withDefault();
    }
}
