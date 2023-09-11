<?php

declare(strict_types=1);

namespace Kelnik\Contact\Platform\Screens;

use Illuminate\Http\RedirectResponse;
use Kelnik\Contact\Http\Requests\OfficeSaveRequest;
use Kelnik\Contact\Models\Office;
use Kelnik\Contact\Platform\Layouts\Office\EditLayout;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layout;
use Orchid\Support\Facades\Toast;

final class OfficeEditScreen extends Screen
{
    private bool $exists = false;
    private ?string $title = null;

    public ?Office $office = null;

    public function query(Office $office): array
    {
        $this->name = trans('kelnik-contact::admin.menu.title');
        $this->exists = $office->exists;

        if ($this->exists) {
            $this->name = $office->title;
        }

        return [
            'office' => $office
        ];
    }

    /** @return Action[] */
    public function commandBar(): array
    {
        return [
            Link::make(trans('kelnik-contact::admin.back'))
                ->icon('bs.arrow-left-circle')
                ->route($this->coreService->getFullRouteName('contact.office.list')),

            Button::make(trans('kelnik-contact::admin.delete'))
                ->icon('bs.trash3')
                ->method('removeOffice')
                ->confirm(trans('kelnik-contact::admin.deleteConfirm', ['title' => $this->title]))
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

    public function saveOffice(OfficeSaveRequest $request): RedirectResponse
    {
        $office = $this->office;
        $office->fill($request->getDto()->toArray());

        if ($this->officeRepository->save($office)) {
            Toast::info(trans('kelnik-contact::admin.saved'));
        }

        return redirect()->route(
            $this->coreService->getFullRouteName('contact.office.list')
        );
    }

    public function removeOffice(Office $office): RedirectResponse
    {
        $this->officeRepository->delete($office)
            ? Toast::info(trans('kelnik-contact::admin.deleted'))
            : Toast::warning('An error has occurred');

        return redirect()->route($this->coreService->getFullRouteName('contact.office.list'));
    }
}
