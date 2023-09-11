<?php

declare(strict_types=1);

namespace Kelnik\Page\Database\Seeders;

use Illuminate\Database\Seeder;
use Kelnik\Core\Models\Site;
use Kelnik\Core\Services\Contracts\SiteService;
use Kelnik\Page\Models\Enums\Type;
use Kelnik\Page\Models\Page;
use Kelnik\Page\Models\PageComponent;
use Kelnik\Page\Services\Contracts\HttpErrorService;
use Kelnik\Page\View\Components\ErrorInfo\ErrorInfo;
use Kelnik\Page\View\Components\Header\Enums\Style;
use Kelnik\Page\View\Components\Header\Header;

final class PageErrorSeeder extends Seeder
{
    public function __construct(protected ?Site $site = null)
    {
        $this->site ??= resolve(SiteService::class)->findPrimary();
    }

    public function run(): void
    {
        /** @var Page $page */
        $page = Page::query()->firstOrCreate([
            'site_id' => (int)$this->site?->getKey(),
            'active' => true,
            'slug' => HttpErrorService::PAGE_PATH_SLUG,
            'title' => trans('kelnik-page::seeder.errors.title', [], $this->site?->locale->value),
            'path' => hash('md5', HttpErrorService::PAGE_PATH_SLUG),
            'priority' => Page::PRIORITY_DEFAULT * 5
        ]);

        if (!$page->wasRecentlyCreated) {
            return;
        }

        $page->type = Type::Error;
        $page->saveQuietly();

        foreach ([Header::class, ErrorInfo::class] as $componentName) {
            /** @var PageComponent $component */
            $component = PageComponent::factory()->makeOne([
                'active' => true,
                'component' => $componentName
            ]);
            $component->data->setDefaultValue();

            if ($componentName === Header::class) {
                $val = $component->data->getValue();

                $content = $val->get('content');
                $content['style'] = Style::FixedTransparent->value;

                $val->put('content', $content);
                $component->data->setValue($val);
            }

            $component->page()->associate($page);
            $component->saveQuietly();
        }
    }
}
