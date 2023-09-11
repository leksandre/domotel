<?php

declare(strict_types=1);

namespace Kelnik\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Kelnik\Core\Services\Contracts\SiteService;

final class SeoRobotsController extends Controller
{
    public function __construct(private readonly SiteService $siteService)
    {
    }

    public function __invoke(Request $request): Response
    {
        $site = $this->siteService->current();

        if (!$site || !$site->settings) {
            return response(
                content: config('kelnik-core.site.settings.seo.robots'),
                headers: ['Content-type' => 'text/plain; charset=utf-8']
            );
        }

        return response(
            content: $site->settings->getSeoRobots(),
            headers: [
                'Content-type' => 'text/plain; charset=utf-8',
                'Date' => $site->updated_at->toRfc7231String()
            ]
        );
    }
}
