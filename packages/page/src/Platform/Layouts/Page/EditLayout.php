<?php

declare(strict_types=1);

namespace Kelnik\Page\Platform\Layouts\Page;

use Kelnik\Core\Platform\Fields\Slug;
use Kelnik\Page\Models\Page;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Layouts\Rows;

final class EditLayout extends Rows
{
    protected function fields(): array
    {
        /** @var Page $page */
        $page = $this->query->get('page');

        return [
            Input::make('page.title')
                ->title('kelnik-page::admin.title')
                ->id('field-page-title')
                ->maxlength(255)
                ->required(),
            !$page->type->isSimple()
                ? Input::make('page.slug')->disabled()
                : Slug::make('page.slug')
                    ->title('kelnik-page::admin.slug')
                    ->maxlength(255)
                    ->source('field-page-title')
                    ->help('kelnik-page::admin.slugHelp')
                    ->sourceId((int)$page->getKey())
                    ->method('transliterate'),

            Switcher::make('page.active')
                ->canSee($page->type->isSimple())
                ->title('kelnik-page::admin.active')
                ->sendTrueOrFalse(),

            Input::make('page.css_classes')
                ->title('kelnik-page::admin.cssClasses')
                ->maxlength(255),

            Select::make('page.redirect_type_')
                ->value($page->redirect_type->value)
                ->title('kelnik-page::admin.redirectType')
                ->options($this->query->get('redirect_types'))
                ->canSee($page->type->isSimple())
                ->required(),
            Input::make('page.redirect_url')
                ->title('kelnik-page::admin.redirectUrl')
                ->canSee($page->type->isSimple())
                ->maxlength(255),
            Button::make(trans('kelnik-page::admin.save'))
                ->icon('bs.save')
                ->class('btn btn-secondary')
                ->method('savePage')
        ];
    }
}
