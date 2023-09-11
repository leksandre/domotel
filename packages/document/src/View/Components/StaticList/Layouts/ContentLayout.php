<?php

declare(strict_types=1);

namespace Kelnik\Document\View\Components\StaticList\Layouts;

use Closure;
use Kelnik\Core\Platform\Fields\Title;
use Kelnik\Document\Models\Group;
use Orchid\Screen\Contracts\Fieldable;
use Orchid\Screen\Contracts\Groupable;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Layouts\Rows;

final class ContentLayout extends Rows
{
    public function __construct(private Fieldable|Groupable|Closure $tabFooter, private Fieldable $contentLink)
    {
    }

    protected function fields(): array
    {
        return [
            Input::make('data.content.title')
                ->title('kelnik-document::admin.components.staticList.titleField')
                ->placeholder('kelnik-document::admin.components.staticList.titlePlaceholder')
                ->maxlength(255),

            Title::make('')->value(trans('kelnik-document::admin.components.staticList.groupTitle')),
            Relation::make('data.content.group')
                ->title('kelnik-document::admin.components.staticList.group')
                ->fromModel(Group::class, 'title')
                ->allowEmpty(),

            $this->contentLink,

            is_callable($this->tabFooter) ? call_user_func($this->tabFooter) : $this->tabFooter
        ];
    }
}
