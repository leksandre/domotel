<?php

declare(strict_types=1);

namespace Kelnik\Form\Platform\Screens;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use InvalidArgumentException;
use Kelnik\Form\Models\Form;
use Kelnik\Form\Platform\Layouts\Field\ListLayout;
use Kelnik\Form\Platform\Layouts\Field\ModalLayout;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

final class FieldListScreen extends Screen
{
    public ?Form $form = null;

    public function query(Form $form): array
    {
        $this->name = trans('kelnik-form::admin.menu.fields') . ': ' . $form->title;
        $form->load('fields');

        return [
            'form' => $form,
            'list' => $form->fields,
            'sortableUrl' => route(
                $this->coreService->getFullRouteName('form.field.sort'),
                ['form' => $form],
                false
            ),
            'coreService' => $this->coreService
        ];
    }

    public function commandBar(): array
    {
        return [
            Link::make('kelnik-form::admin.back')
                ->icon('bs.arrow-left-circle')
                ->route($this->coreService->getFullRouteName('form.list')),
            ModalToggle::make(trans('kelnik-form::admin.addField'))
                ->modal('addFormField')
                ->modalTitle(trans('kelnik-form::admin.addFieldHeader'))
                ->action(route(
                    Route::current()->getName(),
                    [
                        'form' => $this->form,
                        'method' => 'addField'
                    ]
                ))
                ->icon('bs.plus-circle')
        ];
    }

    public function layout(): array
    {
        return [
            Layout::modal('addFormField', [ModalLayout::class]),
            ListLayout::class
        ];
    }

    public function addField(Form $form, Request $request): void
    {
        try {
            $res = $this->formPlatformService->addField($form, (string)$request->get('field'));
        } catch (InvalidArgumentException $e) {
            Toast::error($e->getMessage());
            return;
        }

        if ($res) {
            Toast::success(trans('kelnik-form::admin.formFieldAdded'));
            return;
        }

        Toast::error(trans('kelnik-form::admin.error'));
    }

    public function sortable(Request $request): JsonResponse
    {
        $fields = array_values($request->input('elements', []));

        if (!$fields) {
            return Response::json([
                'success' => false,
                'messages' => [trans('kelnik-form::admin.error.emptyList')]
            ]);
        }

        foreach ($fields as $k => &$v) {
            $v = (int) $v;
            if (!$v) {
                unset($fields[$k]);
            }
        }

        $this->formPlatformService->sortFields($fields);

        return Response::json([
            'success' => true,
            'messages' => [trans('kelnik-form::admin.success')]
        ]);
    }
}
