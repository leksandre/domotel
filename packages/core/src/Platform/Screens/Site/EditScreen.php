<?php

declare(strict_types=1);

namespace Kelnik\Core\Platform\Screens\Site;

use Enum;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Kelnik\Core\Models\Enums\Contracts\HasTitle;
use Kelnik\Core\Models\Enums\Lang;
use Kelnik\Core\Models\Enums\Type;
use Kelnik\Core\Models\Site;
use Kelnik\Core\Platform\Layouts\Site\EditLayout;
use Kelnik\Core\Platform\Services\Contracts\SitePlatformService;
use Kelnik\Core\Repositories\Contracts\SiteRepository;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layout;
use Orchid\Support\Facades\Toast;

final class EditScreen extends Screen
{
    private bool $exists = false;
    private ?string $title = null;

    public ?Site $site = null;

    public function __construct(private readonly SitePlatformService $sitePlatformService)
    {
        parent::__construct();
    }

    public function query(Site $site): array
    {
        $this->name = trans('kelnik-core::admin.site.menu');
        $this->exists = $site->exists;

        if ($this->exists) {
            $this->name = $site->title;
        }

        $res = [
            'site' => $site,
            'types' => [],
            'langs' => []
        ];

        /** @var Enum $enum */
        foreach ([Lang::class => 'langs', Type::class => 'types'] as $enum => $varName) {
            foreach ($enum::cases() as $enumVal) {
                $res[$varName][$enumVal->value] = $enumVal instanceof HasTitle
                    ? $enumVal->title()
                    : $enumVal->value;
            }
        }

        return $res;
    }

    /** @return Action[] */
    public function commandBar(): array
    {
        return [
            Link::make(trans('kelnik-core::admin.site.back'))
                ->icon('bs.arrow-left-circle')
                ->route($this->coreService->getFullRouteName('site.list')),

            Button::make(trans('kelnik-core::admin.site.delete'))
                ->icon('bs.trash3')
                ->method('removeSite')
                ->confirm(trans('kelnik-core::admin.site.deleteConfirm', ['title' => $this->title]))
                ->canSee($this->exists),
        ];
    }

    /** @return Layout[]|string[] */
    public function layout(): array
    {
        return [
            EditLayout::class
        ];
    }

    public function saveSite(Request $request): RedirectResponse
    {
        return $this->sitePlatformService->saveSite($this->site, $request);
    }

    public function removeSite(Site $site): RedirectResponse
    {
        resolve(SiteRepository::class)->delete($site)
            ? Toast::info(trans('kelnik-core::admin.site.deleted'))
            : Toast::warning('An error has occurred');

        return redirect()->route($this->coreService->getFullRouteName('site.list'));
    }
}
