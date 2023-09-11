<?php

declare(strict_types=1);

namespace Kelnik\Page\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

/**
 * @property int $id
 * @property int $page_component_route_id
 * @property int $element_id
 * @property string $module_name
 * @property string $model_name
 *
 * @property PageComponentRoute $pageComponentRoute
 * @property PageComponent $pageComponent
 * @property Model $model
 *
 * @method Builder routeOnly()
 */
final class PageComponentRouteElement extends Model
{
    use HasFactory;

    public const DEFAULT_INT_VALUE = 0;

    protected $table = 'page_component_route_module_elements';

    protected $attributes = [
        'page_component_route_id' => self::DEFAULT_INT_VALUE,
        'element_id' => self::DEFAULT_INT_VALUE
    ];

    protected $fillable = [
        'page_component_route_id',
        'element_id',
        'module_name',
        'model_name'
    ];

    public function pageComponentRoute(): BelongsTo
    {
        return $this->belongsTo(PageComponentRoute::class, 'page_component_route_id')->withDefault();
    }

    public function pageComponent(): HasOneThrough
    {
        return $this->hasOneThrough(
            PageComponent::class,
            PageComponentRoute::class,
            'id',
            'id',
            'page_component_route_id',
            'page_component_id'
        )->withDefault();
    }

    public function model(): BelongsTo
    {
        return $this->belongsTo($this->model_name, 'element_id')->withDefault();
    }
}
