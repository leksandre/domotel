<?php

declare(strict_types=1);

namespace Kelnik\Page\Platform\Layouts\ComponentSettings;

use Closure;
use Illuminate\Support\Collection;
use Kelnik\Core\Platform\Fields\ColorPicker;
use Kelnik\Core\Services\Contracts\SettingsService;
use Kelnik\Core\Theme\Contracts\Color;
use Orchid\Screen\Contracts\Fieldable;
use Orchid\Screen\Contracts\Groupable;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Layouts\Rows;

final class ThemeLayout extends Rows
{
    private array $fields = [];
    private SettingsService $settingService;

    public function __construct(private Collection $designFields, private Fieldable|Groupable|Closure $tabFooter)
    {
        $this->settingService = resolve(SettingsService::class);

        if ($this->designFields->has('colors')) {
            $this->initColorFields();
        }
    }

    protected function fields(): array
    {
        return array_merge(
            $this->fields,
            is_callable($this->tabFooter) ? call_user_func($this->tabFooter) : [$this->tabFooter]
        );
    }

    private function initColorFields(): void
    {
        /** @var Collection $componentColors */
        $componentColors = $this->designFields->get('colors');

        if (!$componentColors) {
            return;
        }

        $colorKeys = $componentColors->map(static fn(Color $color) => $color->getName())->toArray();
        $defColors = $this->settingService->getCurrentColors($colorKeys);

        $componentColors->each(function (Color $color) use ($defColors) {
            $this->fields[] = ColorPicker::make('data.theme.colors.' . $color->getFullName())
                ->set('data-default', $defColors[$color->getName()] ?? null)
                ->title($color->getTitle())
                ->label('$' . $color->getFullName());
        });

        $this->fields = [Group::make($this->fields)];
    }
}
