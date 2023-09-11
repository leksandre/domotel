<?php

declare(strict_types=1);

namespace Kelnik\Mortgage\View\Components\HowToBuy;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Kelnik\Core\Platform\Fields\Quill;
use Kelnik\Mortgage\View\Components\HowToBuy\Layouts\BanksLayout;
use Kelnik\Mortgage\View\Components\HowToBuy\Layouts\CalcLayout;
use Kelnik\Mortgage\View\Components\HowToBuy\Layouts\ContentLayout;
use Kelnik\Mortgage\View\Components\HowToBuy\Layouts\SettingsLayout;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\Models\PageComponent;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\RadioButtons;
use Orchid\Screen\Fields\Switcher;
use Orchid\Support\Facades\Layout;

final class DataProvider extends ComponentDataProvider
{
    public const TEXT_LIMIT = 400;
    public const FACTOID_TEXT_LIMIT = 300;
    public const CALC_HELP_TEXT_LIMIT = 400;
    public const CALC_TEXT_LIMIT = 400;
    public const BUTTON_TEXT_LIMIT = 100;
    public const NO_VALUE = '0';
    public const PHONE_LIMIT = 50;
    public const SCHEDULE_LIMIT = 255;

    public function getEditLayouts(): array
    {
        $buttons = Button::make(trans('kelnik-mortgage::admin.save'))
            ->icon('bs.save')
            ->class('btn btn-secondary')
            ->method('saveData');

        return [
            Layout::tabs([
                trans('kelnik-mortgage::admin.components.howToBuy.tabs.content') => new ContentLayout($buttons),
                trans('kelnik-mortgage::admin.components.howToBuy.tabs.banks') => new BanksLayout($buttons),
                trans('kelnik-mortgage::admin.components.howToBuy.tabs.calc') => new CalcLayout($buttons),
                trans('kelnik-mortgage::admin.components.howToBuy.tabs.settings') => new SettingsLayout($buttons),
//                trans('kelnik-news::admin.componentData.headers.design') => new ThemeLayout(
//                    $this->getThemeFields(),
//                    $buttons
//                )
            ]),
            Layout::modal(
                'variant',
                Layout::rows([
                    Input::make('variant.index')->type('hidden'),
                    Input::make('variant.title')->title('kelnik-mortgage::admin.title'),
                    Switcher::make('variant.active')->title('kelnik-mortgage::admin.active'),
                    RadioButtons::make('variant.showBanks')
                        ->title('kelnik-mortgage::admin.components.howToBuy.showBanks.title')
                        ->options([
                            HowToBuy::BANKS_VIEW_OFF => trans(
                                'kelnik-mortgage::admin.components.howToBuy.showBanks.off'
                            ),
                            HowToBuy::BANKS_VIEW_LIST => trans(
                                'kelnik-mortgage::admin.components.howToBuy.showBanks.list'
                            ),
                            HowToBuy::BANKS_VIEW_CALC => trans(
                                'kelnik-mortgage::admin.components.howToBuy.showBanks.calc'
                            )
                        ])
                        ->value(HowToBuy::BANKS_VIEW_OFF),
                    Quill::make('variant.text')->title('kelnik-mortgage::admin.components.howToBuy.text')
                ])
            )->title(trans('kelnik-mortgage::admin.components.howToBuy.modalTitle'))->rawClick()
        ];
    }

    protected function getThemeFields(): Collection
    {
        return collect();
    }

    public function getComponentTitle(): string
    {
        return $this->data->get('content')['title'] ?? parent::getComponentTitle();
    }

    public function getContentFields(): Collection
    {
        return new Collection([
            [
                'name' => 'title',
                'type' => 'string',
                'validate' => 'nullable|max:255'
            ],
            [
                'name' => 'alias',
                'type' => 'string',
                'validate' => 'nullable|max:150|regex:/[a-z0-9\-_]+/i'
            ],
            [
                'name' => 'text',
                'type' => 'string',
                'validate' => 'nullable|max:' . self::TEXT_LIMIT
            ],
            [
                'name' => 'factoidText',
                'type' => 'string',
                'validate' => 'nullable|max:' . self::FACTOID_TEXT_LIMIT
            ],
            [
                'name' => 'openFirstVariant',
                'type' => 'boolean',
                'validate' => 'boolean'
            ]
        ]);
    }

    public function validateSavingRequest(PageComponent $pageComponent, Request $request): void
    {
        $validateRules = [];
        $this->getContentFields()->each(static function ($el) use (&$validateRules) {
            if (isset($el['validate'])) {
                $validateRules['data.content.' . $el['name']] = $el['validate'];
            }
        });
        $validateRules['data.content.button.text'] = 'nullable|max:' . self::BUTTON_TEXT_LIMIT;
        $validateRules['data.content.button.form_id'] = 'nullable|numeric';

        if ($request->has('data.content.variants')) {
            $validateRules['data.content.variants.*.active'] = 'boolean';
            $validateRules['data.content.variants.*.showBanks'] = [
                'required',
                Rule::in([
                    HowToBuy::BANKS_VIEW_OFF,
                    HowToBuy::BANKS_VIEW_LIST,
                    HowToBuy::BANKS_VIEW_CALC
                ])
            ];
            $validateRules['data.content.variants.*.title'] = 'required';
            $validateRules['data.content.variants.*.text'] = 'nullable|string';
        }

        $validateRules['alias'] = 'nullable|max:150|regex:/[a-z0-9\-_]+/i';
        $validateRules['data.banks.id'] = 'nullable|array';
        $validateRules['data.template'] = 'required|max:150|regex:/[a-z0-9\-_]+/i';
        $validateRules['data.calc.card'] = 'required|array';
        $validateRules['data.calc.base'] = 'required|array';
        $validateRules['data.calc.phone'] = 'nullable|max:' . self::PHONE_LIMIT . '|regex:/^\+?[0-9()\- ]+$/i';
        $validateRules['data.calc.schedule'] = 'nullable|max:' . self::SCHEDULE_LIMIT;
        $validateRules['data.calc.text'] = 'nullable|max:' . self::CALC_TEXT_LIMIT;
        $validateRules['data.calc.textHelp'] = 'nullable|max:' . self::CALC_HELP_TEXT_LIMIT;

        $request->validate($validateRules);
    }

    public function setDataFromRequest(PageComponent $pageComponent, Request $request): void
    {
        // Content
        $content = [];

        foreach ($this->getContentFields() as $field) {
            $content[$field['name']] = $request->input('data.content.' . $field['name']);
            if ($field['type'] === 'boolean') {
                $content[$field['name']] = (bool)$content[$field['name']];
            }
        }

        $content['button'] = [
            'text' => trim($request->input('data.content.button.text') ?? ''),
            'form_id' => (int)$request->input('data.content.button.form_id')
        ];

        $tmp = $request->input('data.content.variants', []);
        $content['variants'] = [];

        if ($tmp) {
            foreach ($tmp as $variant) {
                $content['variants'][] = [
                    'active' => (int)($variant['active'] ?? 0),
                    'showBanks' => $variant['showBanks'] ?? HowToBuy::BANKS_VIEW_OFF,
                    'title' => $variant['title'] ?? '',
                    'text' => $variant['text'] ?? ''
                ];
            }
        }
        unset($tmp);

        $this->data->put('content', $content);

        // Banks
        $banks = [
            'id' => $request->input('data.banks.id') ?? [],
            'showRange' => (int)$request->input('data.banks.showRange', 0) === 1
        ];

        if ($banks['id']) {
            foreach ($banks['id'] as $k => &$v) {
                $v = (int) $v;
                if (!$v) {
                    unset($banks['id'][$k]);
                }
            }
            unset($k, $v);
            $banks['id'] = array_values($banks['id']);
        }

        $this->data->put('banks', $banks);

        // Calc
        $calc = $request->input('data.calc') ?? [];
        $calc['base'] = array_map('intval', $calc['base'] ?? []);
        $calc['card'] = array_map('intval', $calc['card'] ?? []);

        foreach ($calc['buttons'] as &$v) {
            $v['form_id'] = (int)$v['form_id'] ?? 0;
        }
        unset($v);

        $this->data->put('calc', $calc);
        unset($calc);

        // Template
        $this->data->put('template', trim($request->input('data.template') ?? ''));
    }

    public function setDefaultValue(): void
    {
        $this->data = new Collection([
            'content' => [
                'title' => $this->getComponentTitleOriginal()
            ]
        ]);
    }
}
