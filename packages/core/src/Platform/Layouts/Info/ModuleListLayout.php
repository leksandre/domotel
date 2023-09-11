<?php

declare(strict_types=1);

namespace Kelnik\Core\Platform\Layouts\Info;

use Kelnik\Core\Models\Contracts\KelnikModuleInfo;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

final class ModuleListLayout extends Table
{
    /** @var string */
    protected $target = 'modules';

    public function __construct()
    {
        $this->title = trans('kelnik-core::admin.about.modules.title');
    }

    /** @return array */
    protected function columns(): array
    {
        return [
            TD::make('title', trans('kelnik-core::admin.about.modules.list.title'))
                ->render(fn(KelnikModuleInfo $value, $loop) => $value->getTitle())
                ->cantHide(),
            TD::make('version', trans('kelnik-core::admin.about.modules.list.version'))
                ->render(fn(KelnikModuleInfo $value, $loop) => $value->getVersion())
                ->cantHide(),
            TD::make('components', trans('kelnik-core::admin.about.modules.list.components'))
                ->render(static function (KelnikModuleInfo $value, $loop) {
                    if (!$value->getComponents()) {
                        return '-';
                    }

                    $components = $value->getComponents();
                    usort($components, static fn($a, $b) => strcmp($a, $b));

                    $res = '<ul class="list-group list-group-flush">';
                    foreach ($components as $v) {
                        $res .= '<li class="list-group-item px-1 py-1">' . $v . '</li>';
                    }
                    $res .= '</ul>';

                    return $res;
                })->cantHide()
        ];
    }
}
