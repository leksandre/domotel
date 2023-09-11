<?php

declare(strict_types=1);

namespace Kelnik\Page\Platform\Layouts\PageComponents;

use Kelnik\Page\View\Components\Contracts\KelnikPageComponentSection;

final class NullListLayout extends BaseListLayout
{
    protected $target = 'components.' . KelnikPageComponentSection::PAGE_COMPONENT_SECTION_NULL;

    public function __construct()
    {
        $this->title = trans('kelnik-page::admin.sections.null');
    }

    public function isSee(): bool
    {
        return $this->query->get($this->target)?->isNotEmpty();
    }
}
