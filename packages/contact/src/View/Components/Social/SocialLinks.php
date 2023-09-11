<?php

declare(strict_types=1);

namespace Kelnik\Contact\View\Components\Social;

use Closure;
use DateInterval;
use DateTimeInterface;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;
use Kelnik\Contact\Models\SocialLink;
use Kelnik\Contact\Repositories\Contracts\SocialLinkRepository;
use Kelnik\Contact\Services\Contracts\ContactService;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Core\View\Components\Contracts\KelnikComponentAlias;
use Kelnik\Core\View\Components\Contracts\KelnikComponentCache;
use Kelnik\Image\ImageFile;
use Kelnik\Image\Params;
use Kelnik\Image\Picture;

final class SocialLinks extends Component implements
    KelnikComponentCache,
    KelnikComponentAlias
{
    protected const CACHE_TTL_DEFAULT = 864000; // 10 days
    private const ICON_WIDTH = 64;
    private const ICON_HEIGHT = 64;

    protected DateTimeInterface|DateInterval|int $cacheTtl = self::CACHE_TTL_DEFAULT;
    private CoreService $coreService;
    private ContactService $contactService;

    public function __construct()
    {
        $this->coreService = resolve(CoreService::class);
        $this->contactService = resolve(ContactService::class);
    }

    public static function getAlias(): string
    {
        return 'kelnik-contact-social-links';
    }

    protected function getTemplateData(): array
    {
        $cacheId = $this->getCacheId();
        $res = Cache::get($cacheId);

        if ($res !== null) {
            return $res;
        }

        $content = [
            'list' => resolve(SocialLinkRepository::class)->getActive()
        ];
        $hasImageModule = $this->coreService->hasModule('image');

        $content['list'] = $content['list']->each(static function (SocialLink $socLink) use ($hasImageModule) {
            if ($socLink->relationLoaded('icon') && $socLink->icon->exists) {
                $socLink->iconPath = $socLink->icon->url();
                $isSvg = mb_strtolower($socLink->icon->extension) === 'svg';

                if (!$isSvg && $hasImageModule) {
                    try {
                        $image = new ImageFile($socLink->icon);
                        $params = new Params($image);
                        $params->width = self::ICON_WIDTH;

                        $socLink->iconPath = Picture::getResizedPath($image, $params);
                        // @codeCoverageIgnoreStart
                    } catch (Exception $e) {
                        // @codeCoverageIgnoreEnd
                    }
                }
            }
        });

        Cache::tags([
            $this->contactService->getSocialCacheTag()
        ])->put($cacheId, $content, $this->cacheTtl);

        return $content;
    }

    public function render(): View|Closure|string|null
    {
        $data = $this->getTemplateData();

        return isset($data['list']) && $data['list']->isNotEmpty()
                ? view('kelnik-contact::components.social.template', $data)
                : null;
    }

    public function getCacheId(): string
    {
        return 'contact_social';
    }
}
