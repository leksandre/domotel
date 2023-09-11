<?php

declare(strict_types=1);

namespace Kelnik\Core\Services\Contracts;

use DateInterval;
use DateTimeInterface;
use Illuminate\Support\Collection;
use Kelnik\Core\Map\Enums\MobileDragMode;
use Kelnik\Core\Models\Setting;
use Kelnik\Core\Repositories\Contracts\AttachmentRepository;
use Kelnik\Core\Repositories\Contracts\SettingsRepository;

interface SettingsService
{
    public const PARAM_COLORS = 'colors';
    public const PARAM_COMPLEX = 'complex';
    public const PARAM_FONTS = 'fonts';
    public const PARAM_MAP = 'map';
    public const PARAM_JS_CODES = 'jsCodes';
    public const PARAM_COOKIE_NOTICE = 'cookieNotice';

    public const FAVICON_MAX_WIDTH = 310;
    public const FAVICON_MAX_HEIGHT = 310;
    public const FAVICON_MIME_TYPE = 'image/png';

    public const JS_CODE_POSITION_HEAD = 'head';
    public const JS_CODE_POSITION_BODY = 'body';

    public function __construct(SettingsRepository $repository, AttachmentRepository $attachmentRepository);

    /**
     * Getting brand colors from SCSS source
     *
     * @param array $colorParams
     *
     * @return array
     */
    public function getDefaultColors(array $colorParams = []): array;

    /**
     * Default colors, merged with colors from settings
     *
     * @param array $colorParams
     *
     * @return array
     */
    public function getCurrentColors(array $colorParams = []): array;

    public function getDefaultFonts(): array;

    public function getComplex(): Collection;

    /**
     * Base color param names
     *
     * @return string[]
     */
    public function getBaseColorNames(): array;

    /**
     * Component color param names
     *
     * @return string[]
     */
    public function getComponentColorNames(): array;

    /**
     * Merged result of component and base colors
     *
     * @return array
     */
    public function getAllColorNames(): array;

    /**
     * Convert color array to Color object
     *
     * @param Collection $colors
     * @param Collection|null $defColors
     *
     * @return Collection
     */
    public function prepareColors(Collection $colors, ?Collection $defColors = null): Collection;

    /**
     * Convert font array to Font object
     *
     * @param Collection $fonts
     *
     * @return Collection
     */
    public function prepareFonts(Collection $fonts): Collection;

    public function getMapDragModeDefault(): MobileDragMode;

    /**
     * Gets settings from cache.
     * If cache is empty then receives data from database and create cache.
     */
    public function getCached(
        string $moduleName,
        string|array $settingName,
        DateTimeInterface|DateInterval|int $cacheTtl = 0
    ): Setting|Collection;

    public function resetSettingCache(string $module, string $name): bool;

    public function getCacheTag(string $module, string $name): string;
}
