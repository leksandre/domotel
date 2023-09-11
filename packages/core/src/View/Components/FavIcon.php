<?php

declare(strict_types=1);

namespace Kelnik\Core\View\Components;

use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;
use Illuminate\View\View;
use Kelnik\Core\Helpers\ImageHelper;
use Kelnik\Core\Providers\CoreServiceProvider;
use Kelnik\Core\Repositories\Contracts\AttachmentRepository;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Core\Services\Contracts\SettingsService;
use Kelnik\Core\View\Components\Contracts\KelnikComponentAlias;
use Kelnik\Core\View\Components\Contracts\KelnikComponentCache;
use Kelnik\Image\ImageFile;
use Kelnik\Image\Params;
use Kelnik\Image\Picture;

final class FavIcon extends Component implements KelnikComponentAlias, KelnikComponentCache
{
    public const SIZES = [
        16, 32, 48, 57, 60, 70, 72, 76, 96, 114, 120, 144, 150, 152, 180, 192, 310
    ];

    public function __construct(
        private readonly SettingsService $settingsService,
        private readonly CoreService $coreService
    ) {
    }

    public static function getAlias(): string
    {
        return 'kelnik-core-favicon';
    }

    private function getTemplateData()
    {
        $cacheId = self::getCacheId();
        $res = Cache::get($cacheId);

        if ($res !== null) {
            return $res;
        }

        $res = [];
        $cacheTags = [
            $this->settingsService->getCacheTag(
                CoreServiceProvider::MODULE_NAME,
                $this->settingsService::PARAM_COMPLEX
            )
        ];

        $complex = $this->settingsService->getComplex();

        if ($complex->isEmpty()) {
            Cache::tags($cacheTags)->forever($cacheId, $res);

            return $res;
        }

        $favIcon = (int)$complex->get('favicon');
        if ($favIcon) {
            $favIcon = resolve(AttachmentRepository::class)->findByPrimary($favIcon);
        }

        if (!$favIcon) {
            Cache::tags($cacheTags)->forever($cacheId, $res);

            return $res;
        }

        $res = [
            'name' => $complex->get('name') ?? '',
            'icon' => $favIcon,
            'sizes' => []
        ];

        $imageModuleExists = $this->coreService->hasModule('image');
        $img = $params = null;

        if ($imageModuleExists) {
            $img = new ImageFile($favIcon);
            $params = new Params($img);
        } else {
            $img = ImageHelper::getImageSizes($favIcon);
        }

        foreach (self::SIZES as $size) {
            if (!$imageModuleExists) {
                if (!empty($img[0]) && $img[0] >= $size) {
                    $res['sizes'][$size] = $favIcon->url();
                }
                continue;
            }

            if ($img->getWidth() < $size) {
                continue;
            }

            $params->width = $size;
            $params->height = $size;
            $params->crop = true;
            $res['sizes'][$size] = Picture::getResizedPath($img, $params);
        }

        Cache::tags($cacheTags)->forever($cacheId, $res);

        return $res;
    }

    public function render(): View|string|null
    {
        $data = $this->getTemplateData();

        return $data
            ? view('kelnik-core::components.favicon', $data)
            : null;
    }

    public function getCacheId(): string
    {
        return 'favicon';
    }
}
