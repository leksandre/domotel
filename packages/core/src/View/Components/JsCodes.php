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

final class JsCodes extends Component implements KelnikComponentAlias, KelnikComponentCache
{
    public function __construct(private readonly SettingsService $settingsService)
    {
    }

    public static function getAlias(): string
    {
        return 'kelnik-core-js-codes';
    }

    private function getTemplateData()
    {
        $cacheId = self::getCacheId();
        $res = Cache::get($cacheId);

        if ($res !== null) {
            return $res;
        }

        $codes = resolve(SettingsRepository::class)->get(
            CoreServiceProvider::MODULE_NAME,
            $this->settingsService::PARAM_JS_CODES
        )?->value ?? new Collection();

        $res = [
            $this->settingsService::JS_CODE_POSITION_HEAD => [],
            $this->settingsService::JS_CODE_POSITION_BODY => []
        ];
        $cacheTags = [
            $this->settingsService->getCacheTag(
                CoreServiceProvider::MODULE_NAME,
                $this->settingsService::PARAM_JS_CODES
            )
        ];

        if ($codes->isEmpty()) {
            Cache::tags($cacheTags)->forever($cacheId, $res);

            return $res;
        }

        $codes->each(static function (array $el) use (&$res) {
            if (!empty($el['active']) && !empty($el['code']) && !empty($el['section']) && isset($res[$el['section']])) {
                $res[$el['section']][] = $el['code'];
            }
        });

        Cache::tags($cacheTags)->forever($cacheId, $res);

        return $res;
    }

    public function render(): View|string|null
    {
        return view('kelnik-core::components.js-codes', ['codes' => $this->getTemplateData()]);
    }

    public function getCacheId(): string
    {
        return 'jscodes';
    }
}
