<?php

namespace Kelnik\Tests\Feature\Core\View\Components\Traits;

trait Page
{
    private function createPage(): \Kelnik\Page\Models\Page
    {
        return \Kelnik\Page\Models\Page::factory()->createOne([
            'site_id' => $this->site->getKey(),
            'active' => true
        ]);
    }
}
