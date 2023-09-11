<?php

declare(strict_types=1);

namespace Kelnik\Page\Platform;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Kelnik\Core\Services\Contracts\SiteService;
use Kelnik\Page\Models\Page;
use Kelnik\Page\Models\PageComponent;
use Kelnik\Page\Repositories\Contracts\PageComponentRepository;
use Kelnik\Page\Repositories\Contracts\PageRepository;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Page\View\Components\Contracts\HasContentAlias;

final class PageAdminController extends Controller
{
    private readonly PageService $pageService;

    public function __construct()
    {
        $this->pageService = resolve(PageService::class);
    }

    public function componentsList(Request $request): array
    {
        /** @var Page $page */
        $pageId = (int)$request->get('pageId', 0);
        $page = resolve(PageRepository::class)->findByPrimary($pageId);
        $res = [];

        if (!$page->exists || $page->components->isEmpty()) {
            return $res;
        }

        $page->components->each(static function (PageComponent $pageComponent) use ($page, &$res) {
            if (is_a($pageComponent->component, HasContentAlias::class, true)) {
                $res[] = [
                    'id' => $pageComponent->id,
                    'title' => $pageComponent->data->getComponentTitle()
                ];
            }
        });

        return $res;
    }

    public function pageOrComponentUrl(Request $request): array
    {
        $pageId = (int)$request->get('pageId', 0);
        $compId = (int)$request->get('compId', 0);

        /** @var Page $page */
        $page = resolve(PageRepository::class)->findByPrimary($pageId);
        $pageComponent = resolve(PageComponentRepository::class)->findByPrimary($compId);
        $absolute = $page->site_id !== resolve(SiteService::class)->findPrimary()?->getKey();

        return [
            'url' => $pageComponent->exists
                ? $this->pageService->getPageComponentUrl($page, $pageComponent, absolute: $absolute)
                : $this->pageService->getPageUrl($page, absolute: $absolute)
        ];
    }
}
