<?php

declare(strict_types=1);

namespace Kelnik\Core\View\Components;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;
use Illuminate\View\View;
use Kelnik\Core\Providers\CoreServiceProvider;
use Kelnik\Core\Repositories\Contracts\SettingsRepository;
use Kelnik\Core\Services\Contracts\SettingsService;
use Kelnik\Core\View\Components\Contracts\KelnikComponentAlias;
use Kelnik\Core\View\Components\Contracts\KelnikComponentCache;

final class CookieNotice extends Component implements KelnikComponentAlias, KelnikComponentCache
{
    public function __construct(private readonly SettingsService $settingsService)
    {
    }

    public static function getAlias(): string
    {
        return 'kelnik-core-cookie-notice';
    }

    private function getTemplateData()
    {
        $cacheId = self::getCacheId();
        $res = Cache::get($cacheId);

        if ($res !== null) {
            return $res;
        }

        $noticeSetting = resolve(SettingsRepository::class)->get(
            CoreServiceProvider::MODULE_NAME,
            $this->settingsService::PARAM_COOKIE_NOTICE,
        )?->value ?? new Collection();

        Cache::tags([
            $this->settingsService->getCacheTag(
                CoreServiceProvider::MODULE_NAME,
                $this->settingsService::PARAM_COOKIE_NOTICE
            )
        ])->forever(
            $cacheId,
            !empty($noticeSetting['active']) ? $noticeSetting : []
        );

        return $noticeSetting;
    }

    public function render(): View|string|null
    {
        $data = $this->getTemplateData();

        return !empty($data['active'])
            ? view('kelnik-core::components.cookie-notice', $data)
            : null;
    }

    public function getCacheId(): string
    {
        return 'cookieNotice';
    }
}
