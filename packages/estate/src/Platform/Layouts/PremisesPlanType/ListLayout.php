<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Layouts\PremisesPlanType;

use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Estate\Models\Contracts\EstateModel;
use Kelnik\Estate\Models\PremisesPlanType;
use Kelnik\Estate\Platform\Layouts\BaseListLayout;
use Orchid\Icons\IconComponent;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\TD;

final class ListLayout extends BaseListLayout
{
    protected string $routeToEdit = 'estate.pplantype.edit';
    protected string $routeToList = 'estate.pplantype.list';

    protected function columns(): array
    {
        /** @var CoreService $coreService */
        $coreService = $this->query->get('coreService');

        return [
            TD::make('title', trans('kelnik-estate::admin.title'))
                ->render(function (PremisesPlanType $model) use ($coreService) {
                    return resolve(IconComponent::class, [
                            'path' => 'kelnik.sort',
                            'width' => '1.5em',
                            'height' => '1.5em',
                            'class' => 'handle me-3 float-start'
                        ])->render()() .
                        ' ' .
                        Link::make('[' . $model->getKey() . '] ' . $model->title)
                            ->route($coreService->getFullRouteName($this->routeToEdit), $model);
                })
                ->filter(Input::make()),
            TD::make('complex', trans('kelnik-estate::admin.premisesPlanType.complex'))
                ->render(function (PremisesPlanType $model) use ($coreService) {
                    return Link::make('[' . $model->complex_id . '] ' . $model->complex->title)
                        ->route($coreService->getFullRouteName('estate.complex.edit'), $model->complex_id);
                }),
            TD::make('created_at', trans('kelnik-estate::admin.created'))
                ->dateTimeString()
                ->defaultHidden(),
            TD::make('updated_at', trans('kelnik-estate::admin.updated'))
                ->dateTimeString()
                ->defaultHidden(),
            TD::make()
                ->render(fn(EstateModel $model) => $this->getControls($model))
                ->cantHide(false)
        ];
    }
}
