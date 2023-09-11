<?php

declare(strict_types=1);

namespace Kelnik\Page\Platform\Layouts\PageComponents;

final class ContentListLayout extends BaseListLayout
{
    public function __construct()
    {
        $this->title = trans('kelnik-page::admin.sections.content');
    }
}
