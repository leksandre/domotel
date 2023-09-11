<?php

declare(strict_types=1);

namespace Kelnik\Tests\Unit\Page\View\Components;

use Kelnik\Page\View\Components\Contracts\KelnikPageComponentSection;
use Kelnik\Page\View\Components\Footer\Footer;
use Kelnik\Tests\TestCase;

final class FooterTest extends TestCase
{
    public function testComponentBelongToHeaderSection()
    {
        $this->assertTrue(
            Footer::getPageComponentSection() === KelnikPageComponentSection::PAGE_COMPONENT_SECTION_FOOTER
        );
    }
}
