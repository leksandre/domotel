<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Kelnik\Core\Helpers\NumberHelper;
use Kelnik\EstateVisual\Http\Resources\Filters\FilterResourceFactory;
use Kelnik\EstateVisual\Models\Filters\Contracts\AbstractFilter;
use Kelnik\EstateVisual\Models\Filters\FloorBase;
use Kelnik\EstateVisual\Models\StepElement;
use Kelnik\EstateVisual\Models\StepElementAngle;
use Kelnik\EstateVisual\Models\StepElementAngleMask;

final class StepResource extends JsonResource
{
    /** @var Collection $resource */
    public $resource;

    public function toArray($request): array
    {
        $step = $this->resource->get('step');

        if (!$step->exists) {
            return [
                'render' => [
                    'link' => null,
                    'shift' => 0
                ],
                'elements' => []
            ];
        }

        /** @var StepElementAngle $angle */
        $angle = $step->angles->first();
        if ($angle) {
            $angle->active = true;
        }
        $baseBorders = $this->resource->get('filters')?->get('baseBorders') ?? new Collection();
        $currentBorders = $this->resource->get('filters')?->get('currentBorders') ?? new Collection();
        $filters = [];

        foreach ($baseBorders as $k => $v) {
            $filter = FilterResourceFactory::make($v['type'], $v, $currentBorders->get($k))->toArray($request);

            if ($filter) {
                $filters[] = $filter;
            }
        }

        if ($this->resource->has('floors')) {
            $filters = array_filter($filters, static fn(array $el) => $el['category'] !== FloorBase::NAME);
            $floorFilter = new FloorBase();
            $floors = [
                'type' => AbstractFilter::TYPE_BUTTON,
                'category' => $floorFilter->getName(),
                'label' => $floorFilter->getTitle(),
                'items' => []
            ];

            /** @var StepElement $el */
            foreach ($this->resource->get('floors') as $el) {
                $floors['items'][$el->getKey()] = [
                    'id' => $el->getKey(),
                    'title' => is_numeric($el->title) ? $el->title : NumberHelper::prepareNumeric($el->title),
                    'priority' => $el->getAttribute('fl_number'),
                    'active' => $el->active,
                    'disabled' => $el->disabled,
                    'amount' => $el->premisesCount
                ];
            }

            $filters[] = $floors;
            unset($floors, $floorFilter);
        }

        $elements = new Collection();

        if ($angle?->masks->isNotEmpty()) {
            $elements = $angle->masks->map(
                fn(StepElementAngleMask $el) => AngleMaskResource::make(
                    $el,
                    $this->resource->get('settings', new Collection()),
                    $this->resource->get('config'),
                    $this->resource->get('elementsRender', new Collection())
                )->toArray($request)
            );
        }

        return [
            'settings' => SettingsResource::make(new Collection(['config' => $this->resource->get('config')])),
            'breadcrumbs' => $this->resource->get('breadcrumbs') ?? [],
            'render' => $angle?->render
                ? AngleRenderResource::make($angle->render, $angle->shift)
                : null,
            'compass' => [
                'degree' => $angle?->degree ?? 0
            ],
            'perspective' => StepAnglesResource::collection($step->angles),
            'filters' => $filters,
            'elements' => $elements->values(),
            'pointers' => AnglePointerResource::collection($angle?->pointers ?? [])
        ];
    }
}
