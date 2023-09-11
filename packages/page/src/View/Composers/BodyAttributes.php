<?php

declare(strict_types=1);

namespace Kelnik\Page\View\Composers;

use Illuminate\Support\Arr;
use Illuminate\View\View;
use Kelnik\Core\Services\Contracts\SettingsService;
use Kelnik\Core\Services\Contracts\SiteService;

final class BodyAttributes
{
    public const ANIMATION_CLASS = 'j-animation';

    public function __construct(
        private readonly SettingsService $settingsService,
        private readonly SiteService $siteService
    ) {
    }

    public function compose(View $view): void
    {
        $complex = $this->settingsService->getComplex();
        $attributes = [];

        if (Arr::get($complex, 'animation.active')) {
            $attributes['class'] = self::ANIMATION_CLASS;
        }

        $site = $this->siteService->current();

        if ($site?->type->isTouch()) {
            $attributes['data-touch'] = 'true';
        }

        $res = '';

        foreach ($attributes as $k => $v) {
            $res .= ' ' . $k . '="' . $v . '"';
        }
        unset($attributes);

        $view->with('bodyAttributes', trim($res));
    }
}
