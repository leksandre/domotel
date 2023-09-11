<?php

declare(strict_types=1);

namespace Kelnik\Form\Platform\Screens;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Kelnik\Form\Models\Form;
use Kelnik\Form\Platform\Layouts\Form\EditLayout;
use Kelnik\Form\Platform\Layouts\Form\EmailLayout;
use Kelnik\Form\Repositories\Contracts\FormRepository;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

final class FormEditScreen extends Screen
{
    private bool $exists = false;
    private ?string $title = null;

    public ?Form $form = null;

    public function query(Form $form): array
    {
        $this->name = trans('kelnik-form::admin.menu.title');
        $this->exists = $form->exists;

        if ($this->exists) {
            $this->name = $form->title;
        }

        $form->load('emails');

        return [
            'form' => $form,
            'coreService' => $this->coreService
        ];
    }

    /** @return Action[] */
    public function commandBar(): array
    {
        return [
            Link::make(trans('kelnik-form::admin.back'))
                ->icon('bs.arrow-left-circle')
                ->route($this->coreService->getFullRouteName('form.list')),

            Button::make(trans('kelnik-form::admin.delete'))
                ->icon('bs.trash3')
                ->method('removeForm')
                ->confirm(trans('kelnik-form::admin.deleteConfirm', ['title' => $this->title]))
                ->canSee($this->exists),
        ];
    }

    public function layout(): array
    {
        return [
            Layout::tabs([
                trans('kelnik-form::admin.tab.base') => EditLayout::class,
                trans('kelnik-form::admin.tab.notify') => EmailLayout::class
            ])
        ];
    }

    public function saveForm(Request $request): RedirectResponse
    {
        return $this->formPlatformService->saveFormFromPlatform($this->form, $request);
    }

    public function removeForm(Form $form): RedirectResponse
    {
        resolve(FormRepository::class)->delete($form)
            ? Toast::info(trans('kelnik-form::admin.deleted'))
            : Toast::warning('An error has occurred');

        return redirect()->route($this->coreService->getFullRouteName('form.list'));
    }

    public function transliterate(Request $request): JsonResponse
    {
        $res = [
            'state' => false,
            'slug' => $request->get('slug')
        ];

        $title = $request->get('source');

        if ($request->get('action') === 'transliterate') {
            $res['slug'] = $title ? $this->formPlatformService->createSlugByTitle($title) : '';
        }
        $repository = resolve(FormRepository::class);
        $formItem = $repository->findByPrimary((int) $request->get('sourceId'));

        $formItem->title = $title;
        $formItem->slug = $request->get('slug') ?? $res['slug'];

        $res['state'] = $repository->isUnique($formItem);

        return Response::json($res);
    }
}
