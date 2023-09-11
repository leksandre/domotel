<?php

declare(strict_types=1);

namespace Kelnik\Document\Platform\Layouts\Category;

use Illuminate\Support\Carbon;
use Kelnik\Core\Platform\Fields\File;
use Kelnik\Core\Platform\Fields\Matrix;
use Kelnik\Core\Platform\Fields\Slug;
use Kelnik\Document\Models\Group;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Layouts\Rows;

final class EditLayout extends Rows
{
    protected function fields(): array
    {
        return [
            Relation::make('category.group_id')
                ->title('kelnik-document::admin.group')
                ->fromModel(Group::class, 'title')
                ->allowEmpty(),
            Input::make('category.title')
                ->title('kelnik-document::admin.title')
                ->id('field-document-title')
                ->maxlength(255)
                ->required(),
            Slug::make('category.slug')
                ->title('kelnik-document::admin.slug')
                ->maxlength(255)
                ->required()
                ->source('field-document-title')
                ->method('transliterate'),
            Switcher::make('category.active')->title('kelnik-document::admin.active')->sendTrueOrFalse(),
            Matrix::make('elements')
                ->title('kelnik-document::admin.elements.sectionTitle')
                ->sortable(true)
                ->columns([
                    'ID' => 'id',
                    trans('kelnik-document::admin.elements.active') => 'active',
                    trans('kelnik-document::admin.elements.title') => 'title',
                    trans('kelnik-document::admin.elements.author') => 'author',
                    trans('kelnik-document::admin.elements.publish_date') => 'publish_date',
                    trans('kelnik-document::admin.elements.attachment') => 'attachment_id',
                ])
                ->fields([
                    'id' => Input::make()
                        ->readonly()
                        ->style('width:30px; background:none; margin:0; padding: .2rem'),
                    'active' => Switcher::make()->sendTrueOrFalse(),
                    'title' => Input::make()->maxlength(255)->required(),
                    'author' => Input::make()->maxlength(255),
                    'publish_date' => DateTimer::make()
                        ->placeholder('kelnik-document::admin.selectDate')
                        ->min(Carbon::create(1970, 1, 1))
                        ->enableTime()
                        ->format24hr()
                        ->allowInput()
                        ->allowEmpty()
                        ->addBeforeRender(function () {
                            if (!$this->get('value')) {
                                $this->set('value', now());
                            }
                        }),
                    'attachment_id' => File::make()->targetId()->groups('document')->class('matrix_picture'),
                ]),
            Button::make(trans('kelnik-document::admin.save'))
                ->icon('bs.save')
                ->class('btn btn-secondary')
                ->method('saveCategory')
        ];
    }
}
