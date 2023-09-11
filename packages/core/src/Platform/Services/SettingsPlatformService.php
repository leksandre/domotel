<?php

declare(strict_types=1);

namespace Kelnik\Core\Platform\Services;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Kelnik\Core\Helpers\ImageHelper;
use Kelnik\Core\Map\Enums\MobileDragMode;
use Kelnik\Core\Models\Setting;
use Kelnik\Core\Providers\CoreServiceProvider;
use Kelnik\Core\Repositories\Contracts\AttachmentRepository;
use Kelnik\Core\Repositories\Contracts\SettingsRepository;
use Kelnik\Core\Services\Contracts\SettingsService;
use Kelnik\Core\Theme\Color;
use Kelnik\Core\Theme\Font;
use Orchid\Attachment\File;
use Orchid\Attachment\Models\Attachment;

final class SettingsPlatformService implements Contracts\SettingsPlatformService
{
    public function __construct(
        private SettingsService $settingsService,
        private SettingsRepository $repository,
        private AttachmentRepository $attachmentRepository
    ) {
    }

    public function getMapDragModeList(): array
    {
        $cases = [];

        foreach (MobileDragMode::cases() as $type) {
            $cases[$type->value] = $type->title();
        }

        return $cases;
    }

    public function saveColors(string $moduleName, array $colors): bool
    {
        $colorParams = $this->settingsService->getDefaultColors();

        foreach ($colors as $k => &$color) {
            if (!$color instanceof \Kelnik\Core\Theme\Contracts\Color) {
                $color = new Color($k, $color, $colorParams[$k] ?? null);
            }
            if (!$color->isDifferentFromDefault() || !$color->getValue()) {
                unset($colors[$k]);
            }
            $color = $color->getValue();
        }
        unset($color);

        $curColorSetting = $this->repository->get($moduleName, $this->settingsService::PARAM_COLORS);
        if (!$curColorSetting) {
            $curColorSetting = new Setting(['module' => $moduleName, 'name' => $this->settingsService::PARAM_COLORS]);
        }
        $curColorSetting->value = collect($colors);

        return $this->repository->set($curColorSetting);
    }

    public function saveFonts(
        string $moduleName,
        array $files = [],
        array $params = []
    ): bool|RedirectResponse {
        $rows = ['regular', 'bold'];
        $fonts = [];

        $curFontsSettings = $this->repository->get($moduleName, $this->settingsService::PARAM_FONTS);

        if (!$curFontsSettings) {
            $curFontsSettings = new Setting(['module' => $moduleName, 'name' => $this->settingsService::PARAM_FONTS]);
        }

        $curFonts = $this->settingsService->prepareFonts($curFontsSettings->value ?? new Collection());

        foreach ($rows as $row) {
            $curValue = $curFonts->first(static fn ($el, $key) => $key === $row);

            $font = $curValue ?? new Font(null);
            $font->setActive(Arr::get($params, $row . '.active', 0) > 0);

            $fontFile = Arr::get($files, $row);
            $deleted = (int)Arr::get($params, $row . '.delete', 0);

            if (!$deleted && $fontFile instanceof UploadedFile) {
                $fileExt = strtolower($fontFile->getClientOriginalExtension());
                if (!in_array($fileExt, config('kelnik-core.theme.fonts.ext'))) {
                    return back()->withErrors(trans('kelnik-core::validation.invalid_file_extension'));
                }
                $attach = (new File(file: $fontFile, group: CoreServiceProvider::MODULE_NAME))->load();
                if ($attach instanceof Attachment) {
                    $font->setFileModel($attach);
                }
            }

            if ($deleted) {
                if ($font->isLoaded()) {
                    $font->delete();
                }
                $font = new Font(null);
            }

            $fonts[$row] = $font->toArray();
        }

        $curFontsSettings->value = collect($fonts);

        return $this->repository->set($curFontsSettings);
    }

    public function saveMap(string $moduleName, array $data): bool
    {
        $mapSetting = $this->repository->get($moduleName, $this->settingsService::PARAM_MAP);

        if (!$mapSetting) {
            $mapSetting = new Setting(['module' => $moduleName, 'name' => $this->settingsService::PARAM_MAP]);
        }

        $mapSetting->value = $data;

        return $this->repository->set($mapSetting);
    }

    public function saveJsCodes(string $moduleName, array $data): bool
    {
        if ($data) {
            $data = array_map(static function (array $el) {
                $el['active'] = !empty($el['active']);

                return $el;
            }, $data);
        }

        $jsCodes = $this->repository->get($moduleName, $this->settingsService::PARAM_JS_CODES);

        if (!$jsCodes) {
            $jsCodes = new Setting(['module' => $moduleName, 'name' => $this->settingsService::PARAM_JS_CODES]);
        }

        $jsCodes->value = $data;

        return $this->repository->set($jsCodes);
    }

    public function saveCookieNotice(string $moduleName, array $data): bool
    {
        $data['active'] = !empty($data['active']);
        $data['expired'] = (int)($data['expired'] ?? self::EXPIRED_DEFAULT);

        if ($data['expired'] > self::EXPIRED_MAX || $data['expired'] < self::EXPIRED_MIN) {
            $data['expired'] = self::EXPIRED_DEFAULT;
        }

        $noticeSetting = $this->repository->get($moduleName, $this->settingsService::PARAM_COOKIE_NOTICE);

        if (!$noticeSetting) {
            $noticeSetting = new Setting([
                'module' => $moduleName,
                'name' => $this->settingsService::PARAM_COOKIE_NOTICE
            ]);
        }

        $noticeSetting->value = $data;

        return $this->repository->set($noticeSetting);
    }

    /** @throws Exception */
    public function saveComplex(string $moduleName, array $data): bool|RedirectResponse
    {
        $validator = Validator::make(
            $data,
            [
                'name' => 'nullable|max:150',
                'phone' => 'nullable|max:50|regex:/^\+?[0-9()\- ]+$/i',
                'email' => 'nullable|email',
                'logoLight' => 'nullable|numeric',
                'logoDark' => 'nullable|numeric',
                'favicon' => 'nullable|numeric',
                'animation.active' => 'boolean',
                'rounding.active' => 'boolean'
            ]
        );

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if (!empty($data['favicon'])) {
            $data['favicon'] = (int) $data['favicon'];
        }

        $data['animation']['active'] = (bool)Arr::get($data, 'animation.active', false);
        $data['rounding']['active'] = (bool)Arr::get($data, 'rounding.active', false);

        $checkIcon = $this->checkIcon($data['favicon'] ?? 0);

        if ($checkIcon instanceof RedirectResponse) {
            return $checkIcon;
        }

        $complexSettings = $this->repository->get($moduleName, $this->settingsService::PARAM_COMPLEX);

        if (!$complexSettings) {
            $complexSettings = new Setting([
                'module' => $moduleName,
                'name' => $this->settingsService::PARAM_COMPLEX
            ]);
        }
        $complexSettings->value = $data;

        $currentLogos = [
            (int)($complexSettings->value?->get('logoLight') ?? 0),
            (int)($complexSettings->value?->get('logoDark') ?? 0),
            (int)($complexSettings->value?->get('favicon') ?? 0)
        ];

        $origLogos = [
            (int)($complexSettings->getOriginal('value')?->get('logoLight') ?? 0),
            (int)($complexSettings->getOriginal('value')?->get('logoDark') ?? 0),
            (int)($complexSettings->getOriginal('value')?->get('favicon') ?? 0)
        ];

        $attachDiff = array_filter(array_diff($origLogos, $currentLogos));

        if ($attachDiff) {
            $this->attachmentRepository->deleteMass($attachDiff);
        }

        return $this->repository->set($complexSettings);
    }

    private function checkIcon(int $icon): false|RedirectResponse
    {
        if (!$icon) {
            return false;
        }

        /**
         * @var Attachment $icon
         * @var AttachmentRepository $attachmentRepo
         */
        $attachmentRepo = resolve(AttachmentRepository::class);
        $attachment = $attachmentRepo->findBypRimary((int)$icon);

        if ($attachment->exists && $attachment->getMimeType() === 'image/png') {
            $sizes = ImageHelper::getImageSizes($attachment);
            if (
                $sizes[0] <= $this->settingsService::FAVICON_MAX_WIDTH
                && $sizes[1] <= $this->settingsService::FAVICON_MAX_HEIGHT
            ) {
                return false;
            }

            return back()->withErrors(
                new MessageBag([
                    trans('kelnik-core::validation.invalid_image_sizes', [
                        'attribute' => trans('kelnik-core::admin.settings.base.favicon.title'),
                        'sizes' => $this->settingsService::FAVICON_MAX_WIDTH .
                            'x' . $this->settingsService::FAVICON_MAX_HEIGHT
                    ])
                ])
            )->withInput();
        }

        $attachmentRepo->delete($attachment);

        return back()->withErrors(
            new MessageBag([
                trans('validation.mimes', [
                    'attribute' => trans('kelnik-core::admin.settings.base.favicon.title'),
                    'values' => 'PNG (' . $this->settingsService::FAVICON_MIME_TYPE . ')'
                ])
            ])
        )->withInput();
    }
}
