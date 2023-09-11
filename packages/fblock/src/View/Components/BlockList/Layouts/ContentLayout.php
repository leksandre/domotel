<?php

declare(strict_types=1);

namespace Kelnik\FBlock\View\Components\BlockList\Layouts;

use Closure;
use Kelnik\Core\Platform\Fields\Picture;
use Kelnik\Core\Platform\Fields\Quill;
use Kelnik\Core\Platform\Fields\Title;
use Kelnik\FBlock\Platform\Services\Contracts\BlockPlatformService;
use Kelnik\FBlock\Providers\FBlockServiceProvider;
use Orchid\Screen\Contracts\Fieldable;
use Orchid\Screen\Contracts\Groupable;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;

final class ContentLayout extends Rows
{
    public function __construct(private Fieldable|Groupable|Closure $tabFooter)
    {
    }

    protected function fields(): array
    {
        return [
            Input::make('data.content.title')
                ->title('kelnik-fblock::admin.components.blockList.titleField')
                ->placeholder('kelnik-fblock::admin.components.blockList.titlePlaceholder')
                ->maxlength(255),

            Quill::make('data.content.text')
                ->title('kelnik-fblock::admin.components.blockList.text')
                ->help('kelnik-fblock::admin.components.blockList.textHelp'),

            Picture::make('data.content.image')
                ->title('kelnik-fblock::admin.components.blockList.image')
                ->groups(FBlockServiceProvider::MODULE_NAME)
                ->targetId(),

            Title::make('')->value(trans('kelnik-fblock::admin.components.blockList.linkTitle')),
            resolve(BlockPlatformService::class)->getContentLink(),
            is_callable($this->tabFooter) ? call_user_func($this->tabFooter) : $this->tabFooter
        ];
    }
}
