<?php

declare(strict_types=1);

namespace Kelnik\Page\View\Components\Header;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\Models\Contracts\DeleteAttach;
use Kelnik\Page\Models\PageComponent;
use Kelnik\Page\View\Components\Header\Enums\Style;
use Kelnik\Page\View\Components\Header\Layouts\ContentLayout;
use Kelnik\Page\View\Components\Header\Layouts\SettingsLayout;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;

final class DataProvider extends ComponentDataProvider
{
    use DeleteAttach;

    public const NO_VALUE = '0';
    public const BUTTON_TEXT_LIMIT = 100;

    public function getEditLayouts(): array
    {
        $buttons = Button::make(trans('kelnik-page::admin.save'))
                        ->icon('bs.save')
                        ->class('btn btn-secondary')
                        ->method('saveData');

        return [
            Layout::tabs([
                 trans('kelnik-page::admin.componentData.headers.content') => new ContentLayout(
                     $buttons,
                     $this->componentNamespace::getMenuTemplates()
                 ),
                 trans('kelnik-page::admin.componentData.headers.settings') => new SettingsLayout($buttons),
//                trans('kelnik-page::admin.componentData.headers.design') => new ThemeLayout(
//                    $this->getThemeFields(),
//                    $buttons
//                )
            ])
        ];
    }

    protected function getThemeFields(): Collection
    {
        return new Collection();
    }

    public function getContentFields(): Collection
    {
        return new Collection([
            [
               'name' => 'logoLight',
               'type' => 'int'
            ],
            [
                'name' => 'logoDark',
                'type' => 'int'
            ],
            [
                'name' => 'logoHeight',
                'type' => 'int',
                'validate' => 'numeric|min:' . Header::LOGO_HEIGHT_MIN . '|max:' . Header::LOGO_HEIGHT_MAX
            ],
            [
                'name' => 'style',
                'type' => 'string',
                'validate' => Rule::in(
                    array_map(fn(Style $case) => $case->value, Style::cases())
                )
            ],
            [
                'name' => 'callbackButton',
                'type' => 'nullable|array'
            ],
            [
                'name' => 'phone',
                'type' => 'string',
                'validate' => 'nullable|max:50|regex:/^\+?[0-9()\- ]+$/i'
            ],
            [
                'name' => 'menu',
                'type' => 'nullable|array'
            ],
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

        $request->validate($validateRules);
    }

    public function setDataFromRequest(PageComponent $pageComponent, Request $request): void
    {
        // Content
        $content = [];
        $oldAttachIds = $this->getAttachIds($this->data);

        foreach ($this->getContentFields() as $field) {
            $content[$field['name']] = $request->input('data.content.' . $field['name']);
        }
        $content['logoLight'] = (int)$content['logoLight'];
        $content['logoDark'] = (int)$content['logoDark'];
        $content['callbackButton']['form_id'] = (int)($content['callbackButton']['form_id'] ?? 0);
        $content['menu'] = array_map(function ($el) {
            $el['id'] = (int) $el['id'] ?? '';

            return $el;
        }, $content['menu'] ?? []);

        $this->data->put('content', $content);

        $newAttachIds = $this->getAttachIds($this->data);

        if ($oldAttachIds) {
            self::$deleteAttachIds = array_diff($oldAttachIds, $newAttachIds);
        }
    }

    public function setDefaultValue(): void
    {
        $this->data = new Collection([
            'style' => Style::Fixed->value
        ]);
    }

    protected function getAttachIds(Collection $originData): array
    {
        return array_filter([
            (int)Arr::get($originData, 'content.logoLight'),
            (int)Arr::get($originData, 'content.logoDark')
        ]);
    }
}
