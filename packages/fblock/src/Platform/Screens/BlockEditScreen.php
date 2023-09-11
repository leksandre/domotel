<?php

declare(strict_types=1);

namespace Kelnik\FBlock\Platform\Screens;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Kelnik\FBlock\Models\FlatBlock;
use Kelnik\FBlock\Platform\Layouts\Block\BaseLayout;
use Kelnik\FBlock\Repositories\Contracts\BlockRepository;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layout;
use Orchid\Support\Facades\Toast;

class BlockEditScreen extends Screen
{
    protected bool $exists = false;
    protected ?string $title = null;

    public ?FlatBlock $element = null;

    /** @return array */
    public function query(FlatBlock $element): array
    {
        $this->name = trans('kelnik-fblock::admin.menu.title');
        $this->exists = $element->exists;

        if ($this->exists) {
            $this->name = $element->title;
        }

        $element['features'] = array_map(fn ($el) => ['title' => $el], $element['features'] ?? []);

        return [
            'element' => $element,
            'coreService' => $this->coreService
        ];
    }

    /** @return Action[] */
    public function commandBar(): array
    {
        return [
            Link::make(trans('kelnik-fblock::admin.back'))
                ->icon('bs.arrow-left-circle')
                ->route($this->coreService->getFullRouteName('fblock.elements')),

            Button::make(trans('kelnik-fblock::admin.delete'))
                ->icon('bs.trash3')
                ->method('removeElement')
                ->confirm(trans('kelnik-fblock::admin.deleteConfirm', ['title' => $this->name]))
                ->canSee($this->exists),
        ];
    }

    /** @return Layout[] */
    public function layout(): array
    {
        return [
            \Orchid\Support\Facades\Layout::tabs([
                 trans('kelnik-fblock::admin.tab.base') => BaseLayout::class
            ])
        ];
    }

    public function saveElement(Request $request): RedirectResponse
    {
        return $this->blockPlatformService->saveElementFromPlatform($this->element, $request);
    }

    public function removeElement(FlatBlock $element): RedirectResponse
    {
        resolve(BlockRepository::class)->delete($element)
            ? Toast::info(trans('kelnik-fblock::admin.deleted'))
            : Toast::warning('An error has occurred');

        return redirect()->route($this->coreService->getFullRouteName('fblock.elements'));
    }
}
