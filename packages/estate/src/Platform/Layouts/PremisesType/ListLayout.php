<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Layouts\PremisesType;

use Kelnik\Estate\Models\PremisesTypeGroup;
use Kelnik\Estate\Platform\Layouts\BaseListLayout;
use Orchid\Icons\IconComponent;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\TD;

final class ListLayout extends BaseListLayout
{
    protected string $routeToEdit = 'estate.ptype.edit';
    protected string $routeToList = 'estate.ptype.list';

    protected function columns(): array
    {
        $coreService = $this->query->get('coreService');

        return [
            TD::make('title', trans('kelnik-estate::admin.title'))
                ->render(
                    fn(PremisesTypeGroup $model) => resolve(IconComponent::class, [
                            'path' => 'kelnik.sort',
                            'width' => '1.5em',
                            'height' => '1.5em',
                            'class' => 'handle me-3 float-start'
                        ])->render()() .
                        ' ' .
                        Link::make('[' . $model->getKey() . '] ' . $model->title)
                            ->route($coreService->getFullRouteName($this->routeToEdit), $model)
                ),
            TD::make('living', trans('kelnik-estate::admin.premisesType.isLiving'))->booleanState(),
            TD::make('image', trans('kelnik-estate::admin.premisesType.imageShort'))
                ->render(
                    fn(PremisesTypeGroup $model) => $model->image->exists
                        ? '<img src="' . $model->image->url() . '" width="100" height="100">'
                        : '-'
                ),
            TD::make('types', trans('kelnik-estate::admin.premisesType.subTypes'))
                ->render(function (PremisesTypeGroup $model) {
                    $types = $model->types->pluck('title')->toArray();
                    return $types ? implode('<br>', $types) : '-';
                }),
            TD::make('created_at', trans('kelnik-estate::admin.created'))
                ->dateTimeString()
                ->defaultHidden(),
            TD::make('updated_at', trans('kelnik-estate::admin.updated'))
                ->dateTimeString()
                ->defaultHidden(),
            TD::make()
                ->render(fn(PremisesTypeGroup $model) => $this->getControls($model))
                ->cantHide()
        ];
    }
}
