<?php

declare(strict_types=1);

namespace Kelnik\Progress\View\Components\Progress\Layouts;

use Closure;
use Kelnik\Core\Platform\Fields\Matrix;
use Kelnik\Core\Platform\Fields\Quill;
use Kelnik\Core\Platform\Fields\Title;
use Kelnik\Progress\Models\Group;
use Orchid\Screen\Contracts\Fieldable;
use Orchid\Screen\Contracts\Groupable;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Layouts\Rows;

final class ContentLayout extends Rows
{
    public function __construct(
        private Fieldable|Groupable|Closure $tabFooter,
        private Field $groupsContentLink,
        private Field $camerasContentLink,
        private Field $albumsContentLink,
    ) {
    }

    protected function fields(): array
    {
        return [
            Input::make('data.content.title')
                ->title('kelnik-progress::admin.components.progress.titleField')
                ->placeholder('kelnik-progress::admin.components.progress.titlePlaceholder')
                ->maxlength(255),

            Quill::make('data.content.text')
                ->title('kelnik-progress::admin.components.progress.text')
                ->help('kelnik-progress::admin.components.progress.textHelp')
                ->hr(),

            Title::make('')->value(trans('kelnik-progress::admin.components.progress.deadlines.title')),
            Matrix::make('data.content.deadlines')
                ->sortable(true)
                ->columns([
                    trans('kelnik-progress::admin.components.progress.deadlines.fieldTitle') => 'title',
                    trans('kelnik-progress::admin.components.progress.deadlines.fieldText') => 'text'
                ])
                ->fields([
                    'title' => Input::make()->maxlength(255),
                    'text' => Input::make()->maxlength(255)
                ])
                ->help('kelnik-progress::admin.components.progress.deadlines.help')
                ->hr(),

            Title::make('')->value(trans('kelnik-progress::admin.components.progress.groupTitle')),
            Relation::make('data.content.group')
                ->title('kelnik-progress::admin.components.progress.group')
                ->fromModel(Group::class, 'title')
                ->allowEmpty(),
            $this->groupsContentLink,

            Title::make('')->value(trans('kelnik-progress::admin.components.progress.cameras')),
            Input::make('data.content.buttonText')
                ->title('kelnik-progress::admin.components.progress.buttonText')
                ->maxlength(255),
            $this->camerasContentLink,

            Title::make('')->value(trans('kelnik-progress::admin.components.progress.albums')),
            $this->albumsContentLink,

            is_callable($this->tabFooter) ? call_user_func($this->tabFooter) : $this->tabFooter
        ];
    }
}
