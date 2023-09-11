<?php

declare(strict_types=1);

namespace Kelnik\Page\Services;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Route;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Kelnik\Core\Services\Contracts\SiteService;
use Kelnik\Page\Models\Page;
use Kelnik\Page\Models\PageStatusBufferDto;
use Kelnik\Page\Repositories\Contracts\PageRepository;
use Kelnik\Page\Services\Contracts\PageComponentBuffer as PageComponentBufferContract;
use Kelnik\Page\Services\Contracts\PageService as PageServiceContract;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

final class HttpErrorService implements Contracts\HttpErrorService
{
    public function executable(HttpExceptionInterface $e, Request $request): bool
    {
        return in_array($e::class, self::EXCEPTIONS);
    }

    public function handle(HttpExceptionInterface $e, Request $request): ?Response
    {
        /** @var PageServiceContract $pageService */
        $pageService = resolve(PageServiceContract::class);

        /** @var Route $route */
        $route = $request->route();
        $siteId = $route instanceof Route
            ? (int)Arr::get($route->defaults, SiteService::ROUTE_PARAM_NAME, 0)
            : 0;

        $siteId = $siteId ?: (int)(resolve(SiteService::class)->findByHost($request->host())?->getKey());
        $page = $this->getErrorPage($siteId, $pageService);

        if (!$page) {
            return null;
        }

        $pageBuffer = new PageStatusBufferDto();
        $pageBuffer->status = $e->getStatusCode();

        /** @var PageComponentBufferContract $buffer */
        $buffer = resolve(PageComponentBufferContract::class);
        $buffer->add($pageBuffer);
        unset($pageBuffer);

        return response(
            view(
                'kelnik-page::page',
                $pageService->getPageContent($page, $request, ['styles', 'scripts', 'footer'])
            ),
            $e->getStatusCode()
        );
    }

    private function getErrorPage(int|string $siteId, PageServiceContract $pageService): ?Page
    {
        $cacheId = $pageService->getPageCacheKey(self::PAGE_PATH_SLUG, $siteId);
        $page = Cache::get($cacheId);

        if ($page !== null) {
            return $page;
        }

        $page =  resolve(PageRepository::class)->getErrorPage($siteId);

        if ($page->exists) {
            Cache::tags($pageService->getPageCacheTag($page->getKey()))->forever($cacheId, $page);
        }

        return ($page?->exists ?? null) ? $page : null;
    }
}
