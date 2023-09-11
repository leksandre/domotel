<?php

declare(strict_types=1);

namespace Kelnik\Page\View\Components\Contracts;

interface KelnikPageComponentSection
{
    public const PAGE_COMPONENT_SECTION_HEADER = 'header';
    public const PAGE_COMPONENT_SECTION_CONTENT = 'content';
    public const PAGE_COMPONENT_SECTION_FOOTER = 'footer';
    public const PAGE_COMPONENT_SECTION_NULL = 'null';

    public static function getPageComponentSection(): string;
}
