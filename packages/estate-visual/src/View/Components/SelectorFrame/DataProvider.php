<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\View\Components\SelectorFrame;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Core\View\Components\Contracts\HasMargin;
use Kelnik\EstateVisual\View\Components\Contracts\HasSearchConfig;
use Kelnik\EstateVisual\View\Components\SelectorFrame\Layouts\FormLayout;
use Kelnik\EstateVisual\View\Components\SelectorFrame\Layouts\SettingsLayout;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\Models\PageComponent;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;

final class DataProvider extends ComponentDataProvider implements HasSearchConfig
{
    public const FORM_TEXT_MAX_LENGTH = 150;
    public const ALIAS_MAX_LENGTH = 150;
    public const NO_VALUE = '0';
    public const IFRAME_TYPE_NARROW = 'narrow';
    public const IFRAME_TYPE_WIDE = 'wide';

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
        $res['plural'] = $this->componentNamespace::getPlural($res['types']);
        $res['popup'] = $this->getPopupId($res['form']['id'] ?? 0);

        $res['iframeType'] = str_contains($this->data->get('frameTemplate'), 'full-width')
            ? self::IFRAME_TYPE_WIDE
            : self::IFRAME_TYPE_NARROW;

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
            'data.alias' => 'nullable|max:150|regex:/[a-z0-9\-_]+/i',
            'data.selector_id' => 'required|numeric',
            'data.types' => 'nullable|array',
            'data.statuses' => 'nullable|array',
            'data.frameTemplate' => 'required|max:150|regex:/[a-z0-9\-_]+/i',
            'data.template' => 'required|max:150|regex:/[a-z0-9\-_]+/i',
            'data.form.id' => 'nullable|numeric',
            'data.form.text' => 'nullable|max:' . self::FORM_TEXT_MAX_LENGTH,
            'data.margin.*' => 'numeric|min:' . HasMargin::MARGIN_MIN . '|max:' . HasMargin::MARGIN_MAX
        ]);
    }

    public function setDataFromRequest(PageComponent $pageComponent, Request $request): void
    {
        $this->data = new Collection([
            'title' => $request->input('data.title'),
            'alias' => $request->input('data.alias'),
            'selector_id' => $request->integer('data.selector_id'),
            'types' => array_filter(array_map(
                'intval',
                $request->input('data.types', [])
            )),
            'statuses' => array_filter(array_map(
                'intval',
                $request->input('data.statuses', [])
            )),
            'frameTemplate' => $request->input('data.frameTemplate'),
            'template' => $request->input('data.template'),
            'form' => [
                'text' => $request->input('data.form.text'),
                'id' => $request->integer('data.form.id')
            ],
            'margin' => array_map('intval', $request->input('data.margin', []))
        ]);
    }

    public function setDefaultValue(): void
    {
        $this->data = new Collection([
            'title' => $this->getComponentTitleOriginal(),
            'frameTemplate' => $this->componentNamespace::getFrameTemplates()->first()?->name,
            'template' => $this->componentNamespace::getTemplates()->first()?->name
        ]);
    }
}
