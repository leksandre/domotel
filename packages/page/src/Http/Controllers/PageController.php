<?php

declare(strict_types=1);

namespace Kelnik\Page\Http\Controllers;

use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Route;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Kelnik\Core\Services\Contracts\SiteService;
use Kelnik\Page\Models\Page;
use Kelnik\Page\Services\Contracts\PageService;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

final class PageController extends Controller
{
    public const PARAM_PAGE_ID = PageService::ROUTE_PARAM_NAME;
    public const PARAM_SITE_ID = SiteService::ROUTE_PARAM_NAME;

    private readonly PageService $pageService;

    public function __construct()
    {
        $this->pageService = resolve(PageService::class);
    }

    public function show(Route $route, Request $request): View|Response|RedirectResponse
    {
        $pageId = (int)Arr::get($route->defaults, self::PARAM_PAGE_ID, 0);
        $siteId = (int)Arr::get($route->defaults, self::PARAM_SITE_ID, 0);

        return $pageId
            ? $this->showPageByKey($pageId, $siteId, $request)
            : $this->showPageByUrl($route->getPrefix() ?? '', $siteId, $request);
    }

    private function showPageByKey(
        int|string $pageId,
        int|string $siteId,
        Request $request
    ): View|Response|RedirectResponse {
        return $this->showPage($this->pageService->getActivePageByPrimary($siteId, $pageId), $request);
    }

    private function showPageByUrl(
        string $pageUrl,
        int|string $siteId,
        Request $request
    ): View|Response|RedirectResponse {
        return $this->showPage($this->pageService->getPageByUrl($siteId, $pageUrl), $request);
    }

    private function showPage(Page $page, Request $request): View|Response|RedirectResponse
    {
        abort_if(!$page->exists, HttpResponse::HTTP_NOT_FOUND);

        if ($page->redirect_type->canBeRedirected() && $page->redirect_url) {
            return redirect($page->redirect_url, $page->redirect_type->value);
        }

        $this->setMeta($page);

        return view(
            'kelnik-page::page',
            $this->pageService->getPageContent($page, $request, ['styles', 'scripts', 'footer'])
        );
    }

    private function setMeta(Page $page): void
    {
        SEOMeta::setTitle($page->title);
        OpenGraph::setTitle($page->title);

        if (!$page->meta) {
            return;
        }

        foreach (['title', 'description', 'keywords'] as $tag) {
            $getMethod = 'get' . ucfirst($tag);
            $setMethod = 'set' . ucfirst($tag);
            $val = $page->meta->{$getMethod}();

            if (!$val) {
                continue;
            }

            SEOMeta::{$setMethod}($val);

            if ($tag === 'keywords') {
                continue;
            }

            OpenGraph::{$setMethod}($val);
        }

        $image = $page->meta->getImage();

        if ($image) {
            OpenGraph::addImage($image->url());
        }
    }
}
