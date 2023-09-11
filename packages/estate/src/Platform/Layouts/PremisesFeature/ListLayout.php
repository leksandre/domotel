<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Layouts\PremisesFeature;

use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Estate\Models\PremisesFeatureGroup;
use Kelnik\Estate\Platform\Layouts\BaseListLayout;
use Orchid\Icons\IconComponent;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\TD;

final class ListLayout extends BaseListLayout
{
    protected string $routeToEdit = 'estate.pfeature.edit';
    protected string $routeToList = 'estate.pfeature.list';

    protected function columns(): array
    {
        /** @var CoreService $coreService */
        $coreService = $this->query->get('coreService');

        return [
            TD::make('title', trans('kelnik-estate::admin.title'))
                ->render(
                    fn(PremisesFeatureGroup $model) => resolve(IconComponent::class, [
                            'path' => 'kelnik.sort',
                            'width' => '1.5em',
                            'height' => '1.5em',
                            'class' => 'handle me-3 float-start'
                        ])->render()() .
                        ' ' .
                        Link::make('[' . $model->getKey() . '] ' . $model->title)
                            ->route($coreService->getFullRouteName($this->routeToEdit), $model)
                ),
            TD::make('general', trans('kelnik-estate::admin.premisesFeature.isGeneral'))->booleanState(),
            TD::make('features', trans('kelnik-estate::admin.premisesFeature.subFeatures'))
                ->render(function (PremisesFeatureGroup $model) {
                    $types = $model->features->pluck('title')->toArray();
                    return $types ? implode('<br>', $types) : '-';
                }),
            TD::make('created_at', trans('kelnik-estate::admin.created'))
                ->dateTimeString()
                ->defaultHidden(),
            TD::make('updated_at', trans('kelnik-estate::admin.updated'))
                ->dateTimeString()
                ->defaultHidden(),
            TD::make()
                ->render(fn(PremisesFeatureGroup $model) => $this->getControls($model))
                ->cantHide()
        ];
    }
}
