<?php

declare(strict_types=1);

namespace Kelnik\EstateSearch\Services;

use Illuminate\Support\Facades\Cache;
use Kelnik\Estate\Services\Contracts\EstateService;
use Kelnik\EstateSearch\Models\Contracts\SearchConfig;
use Kelnik\EstateSearch\Providers\EstateSearchServiceProvider;
use Kelnik\EstateSearch\View\Components\Search\Contracts\HasSearchConfig;
use Kelnik\Page\Models\PageComponent;
use Kelnik\Page\Repositories\Contracts\PageComponentRepository;
use Kelnik\Page\Services\Contracts\PageService;

final class SearchConfigFactory implements Contracts\SearchConfigFactory
{
    public function make(int|string $primaryKey): ?SearchConfig
    {
        if (!strlen((string)$primaryKey)) {
            return null;
        }

        $settings = Cache::get($this->getCacheId($primaryKey));

        if ($settings) {
            return resolve(SearchConfig::class, ['settings' => $settings]);
        }

        /** @var PageComponent $component */
        $component = resolve(PageComponentRepository::class)->findByPrimary($primaryKey);

        if (!$component->exists || !($component->data instanceof HasSearchConfig)) {
            return null;
        }

        $settings = $component->data->getConfigData();
        $pageComponentTag = resolve(PageService::class)->getPageComponentCacheTag($primaryKey);
        $settings['cacheTags'][] = $pageComponentTag;

        Cache::tags([
            resolve(EstateService::class)->getModuleCacheTag(),
            $pageComponentTag,
            EstateSearchServiceProvider::MODULE_NAME
        ])->put($this->getCacheId($primaryKey), $settings);

        return resolve(SearchConfig::class, ['settings' => $settings]);
    }

    private function getCacheId(int|string $primaryKey): string
    {
        return 'estateSearch_config_' . $primaryKey;
    }
}