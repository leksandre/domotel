<?php

declare(strict_types=1);

namespace Kelnik\Estate\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use Kelnik\Estate\Models\Contracts\EstateModelReference;
use Orchid\Screen\AsSource;

/**
 * @property int $id
 * @property int $premises_id
 * @property int $feature_id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 */
final class PremisesFeatureReference extends EstateModelReference
{
    use AsSource;
    use HasFactory;

    protected $table = 'estate_premises_features_reference';
    public $incrementing = true;
}
