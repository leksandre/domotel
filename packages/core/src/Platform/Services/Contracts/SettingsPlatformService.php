<?php

declare(strict_types=1);

namespace Kelnik\Core\Platform\Services\Contracts;

use Illuminate\Http\RedirectResponse;
use Kelnik\Core\Repositories\Contracts\AttachmentRepository;
use Kelnik\Core\Repositories\Contracts\SettingsRepository;
use Kelnik\Core\Services\Contracts\SettingsService;

interface SettingsPlatformService
{
    public const EXPIRED_MIN = 1;
    public const EXPIRED_MAX = 90;
    public const EXPIRED_DEFAULT = 30;
    public const BUTTON_MAX_LENGTH = 150;

    public function __construct(
        SettingsService $settingsService,
        SettingsRepository $repository,
        AttachmentRepository $attachmentRepository
    );

    public function getMapDragModeList(): array;

    public function saveColors(string $moduleName, array $colors): bool;

    public function saveFonts(
        string $moduleName,
        array $files = [],
        array $params = []
    ): bool|RedirectResponse;

    public function saveMap(string $moduleName, array $data): bool;

    public function saveJsCodes(string $moduleName, array $data): bool;

    public function saveCookieNotice(string $moduleName, array $data): bool;

    public function saveComplex(string $moduleName, array $data): bool|RedirectResponse;
}
