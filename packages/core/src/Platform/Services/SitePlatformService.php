<?php

declare(strict_types=1);

namespace Kelnik\Core\Platform\Services;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Kelnik\Core\Models\Enums\Lang;
use Kelnik\Core\Models\Enums\Type;
use Kelnik\Core\Models\Host;
use Kelnik\Core\Models\Site;
use Kelnik\Core\Repositories\Contracts\SiteRepository;
use Kelnik\Core\Services\Contracts\CoreService;
use Orchid\Support\Facades\Toast;

final class SitePlatformService implements Contracts\SitePlatformService
{
    public function __construct(
        private readonly SiteRepository $siteRepository,
        private readonly CoreService $coreService
    ) {
    }

    public function saveSite(Site $site, Request $request): bool|RedirectResponse
    {
        $request->validate([
            'site.title' => 'required|max:255',
            'site.active' => 'required|boolean',
            'site.primary' => 'boolean',
            'site.locale' => [
                'required',
                Rule::enum(Lang::class)
            ],
            'site.type' => [
                'required',
                Rule::enum(Type::class)
            ],
            'site.hosts' => 'nullable|array',
            'site.hosts.*.value' => 'regex:/^[\w0-9.\-]+$/iu',
            'site.settings' => 'nullable|array',
            'site.settings.seo.robots' => 'nullable|string'
        ]);

        $site->fill(Arr::only(
            $request->input('site'),
            ['title', 'active', 'primary', 'locale', 'type']
        ));
        $hosts = [];

        foreach (Arr::pluck($request->input('site.hosts', []), 'value') as $host) {
            $host = trim($host);
            if (!mb_strlen($host)) {
                continue;
            }

            $hosts[] = new Host(['value' => $host]);
        }

        $site->settings->setSeoRobots(
            str_replace("\r", '', $request->input('site.settings.seo.robots') ?? '')
        );

        if (!$this->siteRepository->save($site, $hosts)) {
            return back()->withInput();
        }

        Toast::info(trans('kelnik-core::admin.site.saved'));

        return redirect(route($this->coreService->getFullRouteName('site.list')));
    }
}
