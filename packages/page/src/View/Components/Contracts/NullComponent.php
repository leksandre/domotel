<?php

declare(strict_types=1);

namespace Kelnik\Page\View\Components\Contracts;

use Illuminate\Http\Request;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\Models\PageComponent;
use Kelnik\Page\Providers\PageServiceProvider;

abstract class NullComponent extends KelnikPageComponent
{
    public function render(): null
    {
        return null;
    }

    public static function initDataProvider(): ComponentDataProvider
    {
        return new class (static::class) extends ComponentDataProvider {
            public function getEditLayouts(): array
            {
                return [];
            }

            public function setDataFromRequest(PageComponent $pageComponent, Request $request): void
            {
            }

            public function setDefaultValue(): void
            {
            }

            public function getComponentTitle(): string
            {
                return trans('kelnik-page::admin.components.null.title');
            }

            public function getComponentTitleOriginal(): string
            {
                return '-';
            }

            public function delete(): void
            {
            }
        };
    }

    public static function getAlias(): string
    {
        return 'kelnik-null-component';
    }

    public static function getModuleName(): string
    {
        return PageServiceProvider::MODULE_NAME;
    }

    public static function getTitle(): string
    {
        return '';
    }

    protected function getTemplateData(): iterable
    {
        return [];
    }
}
