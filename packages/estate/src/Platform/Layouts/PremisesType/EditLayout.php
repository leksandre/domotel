<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Layouts\PremisesType;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Kelnik\Core\Platform\Fields\Matrix;
use Kelnik\Core\Platform\Fields\Picture;
use Kelnik\Core\Platform\Fields\Slug;
use Kelnik\Core\Platform\Fields\Title;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Estate\Models\PremisesType;
use Kelnik\Estate\Providers\EstateServiceProvider;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Layouts\Rows;

final class EditLayout extends Rows
{
    protected function fields(): array
    {
        /** @var CoreService $coreService */
        $types = $this->query->get('types');
        $idCollection = Collection::make();
        $titleCollection = Collection::make();
        $coreService = $this->query->get('coreService');

        $fields = [
            Input::make('type.title')
                ->title('kelnik-estate::admin.title')
                ->id('field-type-title')
                ->required(),
            Slug::make('type.slug')
                ->title('kelnik-estate::admin.slug')
                ->required()
                ->maxlength(255)
                ->source('field-type-title')
                ->method('transliterate'),
            Switcher::make('type.living')
                ->title('kelnik-estate::admin.premisesType.isLiving')
                ->sendTrueOrFalse(),
            Switcher::make('type.build_title')
                ->title('kelnik-estate::admin.premisesType.buildTitle')
                ->help('kelnik-estate::admin.premisesType.buildTitleHelp')
                ->sendTrueOrFalse(),
            Input::make('type.external_id')
                ->title('kelnik-estate::admin.external_id')
                ->maxlength(255),
            Picture::make('type.image_id')
                ->title('kelnik-estate::admin.premisesType.image')
                ->storage(config('kelnik-estate.storage.disk'))
                ->groups(EstateServiceProvider::MODULE_NAME)
                ->targetId()
                ->hr(),

            Title::make('')->value(trans('kelnik-estate::admin.premisesType.plural.title')),
            Input::make('type.plural.0')
                ->placeholder('kelnik-estate::admin.premisesType.plural.placeholder.0')
                ->maxlength(100)
                ->style('width: 30%'),
            Input::make('type.plural.1')
                ->placeholder('kelnik-estate::admin.premisesType.plural.placeholder.1')
                ->maxlength(100)
                ->style('width: 30%'),
            Input::make('type.plural.2')
                ->placeholder('kelnik-estate::admin.premisesType.plural.placeholder.2')
                ->maxlength(100)
                ->style('width: 30%')
                ->hr(),

            Matrix::make('type.types')
                ->sortable(true)
                ->columns(
                    [
                        'ID' => 'id',
                        trans('kelnik-estate::admin.title') => 'title',
                        trans('kelnik-estate::admin.shortTitle') => 'short_title',
                        trans('kelnik-estate::admin.slug') => 'slug',
                        trans('kelnik-estate::admin.external_id') => 'external_id',
                        trans('kelnik-estate::admin.premisesType.rooms') => 'rooms',
                        trans('kelnik-estate::admin.premisesType.replace') => 'replace_id'
                    ]
                )
                ->title('kelnik-estate::admin.premisesType.subTypes')
                ->fields(
                    [
                        'id' => Input::make()
                            ->readonly()
                            ->addBeforeRender(function () use ($idCollection) {
                                $idCollection->add(
                                    ['id' => Arr::get($this->attributes, 'value', 0)]
                                );
                            }),
                        'title' => Input::make()
                            ->required()
                            ->maxlength(255)
                            ->addBeforeRender(function () use ($titleCollection) {
                                $titleCollection->add(
                                    ['id' => Arr::get($this->attributes, 'id')]
                                );
                            }),
                        'short_title' => Input::make()
                            ->required()
                            ->maxlength(255)
                            ->addBeforeRender(function () use ($titleCollection) {
                                $titleCollection->add(
                                    ['id' => Arr::get($this->attributes, 'id')]
                                );
                            }),
                        'slug' => Slug::make()
                            ->required()
                            ->maxlength(255)
                            ->method('transliterateType')
                            ->addBeforeRender(function () use ($idCollection, $titleCollection) {
                                $this->set('source', Arr::get($titleCollection->last(), 'id'));
                                $this->set('sourceId', Arr::get($idCollection->last(), 'id', 0));
                            }),
                        'external_id' => Input::make()->maxlength(255),
                        'rooms' => Input::make()
                            ->type('number')
                            ->min(0)
                            ->value(PremisesType::ROOMS_DEFAULT),
                        'replace_id' => Select::make()
                            ->options($types)
                            ->addBeforeRender(function () use ($idCollection, $types) {
                                $rowId = (int)Arr::get($idCollection->last(), 'id', 0);

                                if ($rowId) {
                                    $types->reject(
                                        function ($value, $key) use ($rowId) {
                                            return $key === $rowId;
                                        }
                                    );
                                }
                                $types->prepend(trans('kelnik-estate::admin.premisesType.empty'), '0');
                                $this->set('options', $types->toArray());
                            })
                    ]
                )
        ];

        if ($coreService->hasModule('page')) {
            $pageIds = $this->query->get('pageIds');

            /** @var array $site */
            foreach ($this->query->get('pageOptions') as $site) {
                $fields[] = Select::make('page.' . $site['id'])
                    ->title(
                        trans('kelnik-estate::admin.premisesType.modelToPage', ['site' => $site['title']])
                    )
                    ->help('kelnik-estate::admin.premisesType.modelToPageHelp')
                    ->value(
                        Arr::get($pageIds, $site['id'] . '.' . $this->query->get('type')->getKey())
                    )
                    ->options($site['pages']);
            }
        }

        $fields[] = Button::make(trans('kelnik-estate::admin.save'))
                ->icon('bs.save')
                ->class('btn btn-secondary')
                ->method('saveRow');

        return $fields;
    }
}
