<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Layouts\ImportPlan;

use Kelnik\Core\Platform\Fields\SelectNative;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Estate\Models\Complex;
use Kelnik\Estate\Platform\Services\Contracts\ImportPlanPlatformService;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\RadioButtons;
use Orchid\Screen\Layouts\Rows;

final class Form extends Rows
{
    private const NO_VALUE = '0';

    protected $template = 'kelnik-estate::platform.layouts.import';

    protected function fields(): array
    {
        /** @var CoreService $coreService */
        $coreService = resolve(CoreService::class);

        return [
            SelectNative::make('complex')
                ->options(
                    $this->query->get('complexes')
                        ?->map(fn(Complex $complex) => [
                            'id' => $complex->getKey(),
                            'title' => '[' . $complex->getKey() . '] ' . $complex->title
                        ])
                        ->pluck('title', 'id')
                        ->toArray()
                )
                ->empty(trans('kelnik-estate::admin.import.setComplex'))
                ->set('data-estate-import_plan-target', 'complex')
                ->set(
                    'data-action',
                    route(
                        $coreService->getFullRouteName('estate.importPlan'),
                        ['method' => 'getComplexChilds'],
                        false
                    )
                ),

            SelectNative::make('building')
                ->empty(trans('kelnik-estate::admin.import.setBuilding'), self::NO_VALUE)
                ->disabled()
                ->set('data-estate-import_plan-target', 'building')
                ->set(
                    'data-action',
                    route(
                        $coreService->getFullRouteName('estate.importPlan'),
                        ['method' => 'getBuildingChilds'],
                        false
                    )
                ),

            SelectNative::make('section')
                ->empty(trans('kelnik-estate::admin.import.setSection'), self::NO_VALUE)
                ->disabled()
                ->set('data-estate-import_plan-target', 'section')
                ->set(
                    'data-action',
                    route(
                        $coreService->getFullRouteName('estate.importPlan'),
                        ['method' => 'getSectionChilds'],
                        false
                    )
                ),

            SelectNative::make('floor')
                ->empty(trans('kelnik-estate::admin.import.setFloor'), self::NO_VALUE)
                ->disabled()
                ->multiple()
                ->set('data-estate-import_plan-target', 'floor')
                ->set('style', 'height: 200px !important')
                ->hr(),

            Input::make('plan')
                ->title('kelnik-estate::admin.import.file.plan')
                ->type('file')
                ->accept('image/*')
                ->multiple()
                ->help('kelnik-estate::admin.import.file.help')
                ->set('data-estate-import_plan-target', 'plan'),

            Input::make('searchPlan')
                ->title('kelnik-estate::admin.import.file.searchPlan')
                ->type('file')
                ->accept('image/*')
                ->multiple()
                ->help('kelnik-estate::admin.import.file.help')
                ->set('data-estate-import_plan-target', 'searchPlan'),

            Input::make('floorPlan')
                ->title('kelnik-estate::admin.import.file.floorPlan')
                ->type('file')
                ->accept('image/*')
                ->multiple()
                ->help('kelnik-estate::admin.import.file.help')
                ->set('data-estate-import_plan-target', 'floorPlan'),

            RadioButtons::make('type')
                ->title('kelnik-estate::admin.import.type.title')
                ->options([
                    ImportPlanPlatformService::TYPE_NUMBER_ON_FLOOR => trans(
                        'kelnik-estate::admin.import.type.numberOnFloor'
                    ),
                    ImportPlanPlatformService::TYPE_NUMBER => trans('kelnik-estate::admin.import.type.number')
                ])
                ->value(ImportPlanPlatformService::TYPE_NUMBER_ON_FLOOR)
                ->set('data-estate-import_plan-target', 'type')
                ->help(trans('kelnik-estate::admin.import.type.help'))
                ->hr(),



            Button::make(trans('kelnik-estate::admin.import.button'))
                ->icon('bs.save')
                ->class('btn btn-secondary')
                ->set('data-estate-import_plan-target', 'button')
                ->set('data-action', 'click->estate-import_plan#submit')
                ->set('data-translates', json_encode([
                    'error' => trans('kelnik-estate::admin.error'),
                    'requestError' => trans('kelnik-estate::admin.errors.request'),
                    'floorError' => trans('kelnik-estate::admin.errors.floors'),
                    'fileError' => trans('kelnik-estate::admin.errors.files'),
                    'plan' => trans('kelnik-estate::admin.import.file.plan'),
                    'searchPlan' => trans('kelnik-estate::admin.import.file.searchPlan'),
                    'floorPlan' => trans('kelnik-estate::admin.import.file.floorPlan')
                ]))
                ->set('data-form-action', route(
                    $coreService->getFullRouteName('estate.importPlan'),
                    ['method' => 'import'],
                    false
                ))
                ->rawClick(true)
                ->addBeforeRender(function () use ($coreService) {
                    $this->set('action', null);
                    $this->set('formaction', null);
                })
        ];
    }
}
