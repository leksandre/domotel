<?php

declare(strict_types=1);

namespace Kelnik\Page\View\Components\Footer\Layouts;

use Closure;
use Kelnik\Core\Platform\Fields\Picture;
use Kelnik\Core\Platform\Fields\Quill;
use Kelnik\Core\Platform\Fields\Title;
use Kelnik\Page\Providers\PageServiceProvider;
use Orchid\Screen\Contracts\Fieldable;
use Orchid\Screen\Contracts\Groupable;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;

final class ContentLayout extends Rows
{
    protected const BUTTON_TEXT_LIMIT = 100;
    protected const BUTTON_LINK_LIMIT = 150;

    public function __construct(private Fieldable|Groupable|Closure $tabFooter)
    {
    }

    protected function fields(): array
    {
        $res = [
            Title::make('')->value(trans('kelnik-page::admin.components.footer.logo.title')),
            Picture::make('data.content.logo')
                ->title('kelnik-page::admin.components.footer.logo.file')
                ->targetId()
                ->groups(PageServiceProvider::MODULE_NAME),
            Input::make('data.content.link')
                ->title('kelnik-page::admin.components.footer.logo.link')
                ->help(
                    trans(
                        'kelnik-page::admin.components.header.callback.limit',
                        ['limit' => self::BUTTON_LINK_LIMIT]
                    )
                )
                ->maxlength(self::BUTTON_LINK_LIMIT)
                ->hr(),

            Title::make('')->value(trans('kelnik-page::admin.components.footer.textTitle')),
            Quill::make('data.content.text')->title('kelnik-page::admin.components.footer.text'),

            Input::make('data.content.copyright')
                ->title('kelnik-page::admin.components.footer.copyright')
                ->help(
                    trans(
                        'kelnik-page::admin.components.footer.limit',
                        ['limit' => 255]
                    )
                )
                ->maxlength(255)
                ->hr(),

            Title::make('')->value(trans('kelnik-page::admin.components.footer.policy.title')),
            Input::make('data.content.policyText')
                ->title('kelnik-page::admin.components.footer.policy.text')
                ->help(
                    trans(
                        'kelnik-page::admin.components.footer.limit',
                        ['limit' => self::BUTTON_TEXT_LIMIT]
                    )
                )
                ->maxlength(self::BUTTON_TEXT_LIMIT),
            Input::make('data.content.policyLink')
                ->title('kelnik-page::admin.components.footer.policy.link')
                ->help(
                    trans(
                        'kelnik-page::admin.components.footer.limit',
                        ['limit' => self::BUTTON_LINK_LIMIT]
                    )
                )
                ->maxlength(self::BUTTON_LINK_LIMIT)
                ->hr()
        ];

        $res[] = is_callable($this->tabFooter) ? call_user_func($this->tabFooter) : $this->tabFooter;

        return $res;
    }
}
