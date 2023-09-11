<?php

declare(strict_types=1);

namespace Kelnik\Estate\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Kelnik\Core\Services\Contracts\SiteService;
use Kelnik\Estate\View\Components\PremisesCard\PremisesCard;
use Kelnik\Estate\View\Components\PremisesCard\PremisesCardToPdf;
use Kelnik\Estate\View\Components\PremisesCard\RouteProvider;
use Kelnik\Page\Models\Page;
use Kelnik\Page\Models\PageComponent;
use Kelnik\Page\Services\Contracts\PageService;
use Symfony\Component\HttpFoundation\Response;

final class PrintToPdf extends Controller
{
    private readonly PageService $pageService;
    private readonly SiteService $siteService;

    public function __construct()
    {
        $this->pageService = resolve(PageService::class);
        $this->siteService = resolve(SiteService::class);
    }

    public function __invoke(Request $request, int $pageId, int $pageComponentId): Response
    {
        $route = Route::current();
        $primary = (int)$route->parameter(RouteProvider::PARAM_KEY);

        abort_if(!$primary, Response::HTTP_NOT_FOUND);

        /**
         * @var Page $page
         * @var PageComponent $pageComponent
         */
        [$page, $pageComponent] = $this->pageService->getActivePageWithComponent(
            $this->siteService->current()?->getKey(),
            $pageId,
            $pageComponentId
        );

        abort_if(
            !$pageComponent->exists || !is_a($pageComponent->component, PremisesCard::class, true),
            Response::HTTP_NOT_FOUND
        );

        return (new PremisesCardToPdf(
            $primary,
            $route->getName(),
            $pageComponent->getKey(),
            $pageComponent->data?->get('pdf') ?? []
        ))->send();
    }
}
