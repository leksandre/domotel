<?php

declare(strict_types=1);

namespace Kelnik\Mortgage\Platform\Screens;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Kelnik\Mortgage\Models\Bank;
use Kelnik\Mortgage\Platform\Layouts\Bank\BankBaseLayout;
use Kelnik\Mortgage\Platform\Layouts\Bank\BankProgramsLayout;
use Kelnik\Mortgage\Repositories\Contracts\BankRepository;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layout;
use Orchid\Support\Facades\Toast;

final class BankEditScreen extends Screen
{
    private bool $exists = false;
    private ?string $title = null;

    public ?Bank $bank = null;

    public function query(Bank $bank): array
    {
        $this->name = trans('kelnik-mortgage::admin.menu.title');
        $this->exists = $bank->exists;

        if ($this->exists) {
            $this->name = $bank->title;
        }

        return [
            'bank' => $bank,
            'programs' => $bank->programs
        ];
    }

    /** @return Action[] */
    public function commandBar(): array
    {
        return [
            Link::make(trans('kelnik-mortgage::admin.back'))
                ->icon('bs.arrow-left-circle')
                ->route($this->coreService->getFullRouteName('mortgage.banks')),

            Button::make(trans('kelnik-mortgage::admin.delete'))
                ->icon('bs.trash3')
                ->method('removeBank')
                ->confirm(trans('kelnik-mortgage::admin.deleteConfirm', ['title' => $this->title]))
                ->canSee($this->exists),
        ];
    }

    /** @return Layout[]|string[] */
    public function layout(): array
    {
        return [
            \Orchid\Support\Facades\Layout::tabs([
                trans('kelnik-mortgage::admin.tab.base') => BankBaseLayout::class,
                trans('kelnik-mortgage::admin.tab.programs') => BankProgramsLayout::class
            ])
        ];
    }

    public function saveBank(Request $request): RedirectResponse
    {
        return $this->mortgagePlatformService->saveBank($this->bank, $request);
    }

    public function removeBank(Bank $bank): RedirectResponse
    {
        resolve(BankRepository::class)->delete($bank)
            ? Toast::info(trans('kelnik-mortgage::admin.deleted'))
            : Toast::warning('An error has occurred');

        return redirect()->route($this->coreService->getFullRouteName('mortgage.banks'));
    }
}
