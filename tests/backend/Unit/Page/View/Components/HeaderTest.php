<?php

declare(strict_types=1);

namespace Kelnik\Tests\Unit\Page\View\Components;

use Kelnik\Page\View\Components\Contracts\KelnikPageComponentSection;
use Kelnik\Page\View\Components\Header\Header;
use Kelnik\Tests\TestCase;

final class HeaderTest extends TestCase
{
    public function testComponentBelongToHeaderSection()
    {
        $this->assertTrue(
            Header::getPageComponentSection() === KelnikPageComponentSection::PAGE_COMPONENT_SECTION_HEADER
        );
    }
}
