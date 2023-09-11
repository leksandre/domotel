<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\View\Components\Selector;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\EstateVisual\View\Components\Contracts\HasSearchConfig;
use Kelnik\EstateVisual\View\Components\Selector\Layouts\FormLayout;
use Kelnik\EstateVisual\View\Components\Selector\Layouts\SettingsLayout;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\Models\PageComponent;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;

final class DataProvider extends ComponentDataProvider implements HasSearchConfig
{
    public const FORM_TEXT_MAX_LENGTH = 150;
    public const NO_VALUE = '0';

    public function getEditLayouts(): array
    {
        $buttons = Button::make(trans('kelnik-estate-visual::admin.save'))
                        ->icon('bs.save')
                        ->class('btn btn-secondary')
                        ->method('saveData');

        $layouts = [new SettingsLayout()];

        if (resolve(CoreService::class)->hasModule('form')) {
            $layouts[] = new FormLayout();
        }

        $layouts[] = Layout::rows([$buttons]);

        return $layouts;
    }

    public function getComponentTitle(): string
    {
        return $this->data->get('title') ?? parent::getComponentTitle();
    }

    public function getConfigData(): array
    {
        $res = Arr::only($this->data->toArray(), ['types', 'statuses', 'template', 'form']);
        $res['popup'] = $this->getPopupId($res['form']['id'] ?? 0);

        return $res;
    }

    public function getPopupId(int|string $formId): string
    {
        return $formId ? 'visual-form-' . $formId : '';
    }

    public function validateSavingRequest(PageComponent $pageComponent, Request $request): void
    {
        $request->validate([
            'data.title' => 'nullable|max:255',
            'data.selector_id' => 'required|numeric',
            'data.types' => 'nullable|array',
            'data.statuses' => 'nullable|array',
            'data.template' => 'required|max:150|regex:/[a-z0-9\-_]+/i',
            'data.form.id' => 'nullable|numeric',
            'data.form.text' => 'nullable|max:' . self::FORM_TEXT_MAX_LENGTH,
        ]);
    }

    public function setDataFromRequest(PageComponent $pageComponent, Request $request): void
    {
        $this->data = new Collection([
            'title' => $request->input('data.title'),
            'selector_id' => (int)$request->input('data.selector_id'),
            'types' => array_filter(array_map(
                'intval',
                $request->input('data.types', [])
            )),
            'statuses' => array_filter(array_map(
                'intval',
                $request->input('data.statuses', [])
            )),
            'template' => $request->input('data.template'),
            'form' => [
                'text' => $request->input('data.form.text'),
                'id' => $request->integer('data.form.id')
            ]
        ]);
    }

    public function setDefaultValue(): void
    {
        $this->data = new Collection([
            'title' => $this->getComponentTitleOriginal(),
            'template' => $this->componentNamespace::getTemplates()->first()?->name
        ]);
    }
}
