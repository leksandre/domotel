<?php

declare(strict_types=1);

namespace Kelnik\Core\View\Components\Theme;

use Illuminate\View\Component;
use Illuminate\View\View;
use Kelnik\Core\View\Components\Contracts\KelnikComponentAlias;

final class ComponentTheme extends Component implements KelnikComponentAlias
{
    public function __construct(private string $selector, private null|string|iterable $theme)
    {
        if (is_array($this->theme)) {
            $this->theme = collect($this->theme);
        }
    }

    public static function getAlias(): string
    {
        return 'kelnik-core-component-theme';
    }

    public function render(): View|string|null
    {
        return view(
            'kelnik-core::components.component-theme',
            [
                'selector' => $this->selector,
                'colors' => collect($this->theme->get('colors'))
            ]
        );
    }
}
