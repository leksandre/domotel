<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Http\Resources\Filters;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Kelnik\EstateVisual\Http\Controllers\SelectorController;
use Kelnik\EstateVisual\Models\Steps\Factory;

/**
 * @property Collection $resource
 */
final class CheckboxResource extends JsonResource
{
    private const PARAM_REQUEST_STEP = SelectorController::PARAM_REQUEST_STEP;

    public function __construct($resource, private readonly ?Collection $curData = null)
    {
        parent::__construct($resource);
    }

    public function toArray($request): ?array
    {
        $values = $this->resource->get('values');
        $res = [
            'type' => $this->resource->get('type'),
            'category' => $this->resource->get('name'),
            'label' => $this->resource->get('title')
        ];

        foreach ($values as $val) {
            $res['items'][$val['id']] = [
                'id' => $val['id'],
                'value' => $val['id'],
                'name' => $val['title'],
                'title' => [
                    'full' => $val['title'],
                    'short' => $val['short_title'] ?? $val['title']
                ],
                'checked' => $this->curData->isNotEmpty()
                    && in_array($val['id'], $this->curData->get('selected') ?? []),
                'disabled' => $this->curData->isNotEmpty() && !isset($this->curData->get('values')[$val['id']])
            ];

            if ($request->get(self::PARAM_REQUEST_STEP) === Factory::STEP_FLOOR) {
                $res['items'][$val['id']]['color'] = $val['color'];
            }
        }

        return isset($res['items'])
            ? $res
            : null;
    }
}
