<?php

declare(strict_types=1);

namespace Kelnik\Page\View\Components\FirstScreen;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Kelnik\Core\Helpers\NumberHelper;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\Models\Contracts\DeleteAttach;
use Kelnik\Page\Models\PageComponent;
use Kelnik\Page\Rules\VideoLink;
use Kelnik\Page\View\Components\FirstScreen\Layouts\ContentLayout;
use Kelnik\Page\View\Components\FirstScreen\Layouts\SettingsLayout;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;

final class DataProvider extends ComponentDataProvider
{
    use DeleteAttach;

    public const ACTION_BUTTON_TEXT_LIMIT = 100;
    public const ACTION_BUTTON_LINK_LIMIT = 150;

    public const DEFAULT_BG_COLOR = '#ffffff';

    public function getEditLayouts(): array
    {
        $buttons = Button::make(trans('kelnik-page::admin.save'))
                        ->icon('bs.save')
                        ->class('btn btn-secondary')
                        ->method('saveData');

        return [
            Layout::tabs([
                 trans('kelnik-page::admin.componentData.headers.content') => new ContentLayout($buttons),
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
        return collect();
    }

    public function getComponentTitle(): string
    {
        return $this->data->get('content')['slogan'] ?? parent::getComponentTitle();
    }

    public function getContentFields(): Collection
    {
        return new Collection([
            [
                'name' => 'slogan',
                'type' => 'string',
                'validate' => 'nullable|max:255'
            ],
            [
                'name' => 'complexName',
                'type' => 'string',
                'validate' => 'nullable|max:255'
            ],
            [
                'name' => 'alias',
                'type' => 'string',
                'validate' => 'nullable|max:150|regex:/[a-z0-9\-_]+/i'
            ],
            [
               'name' => 'action',
               'validate' => 'nullable|array'
            ],
            [
                'name' => 'fullHeight',
                'type' => 'string'
            ],
            [
                'name' => 'fullWidth',
                'type' => 'bool'
            ],
            [
                'name' => 'animated',
                'type' => 'bool'
            ],
            [
                'name' => 'video',
                'type' => 'string',
                'validate' => ['nullable', 'url', new VideoLink()]
            ],
            [
                'name' => 'slider',
                'type' => 'array',
                'validate' => 'nullable|array',
            ],
            [
                'name' => 'advantages',
                'type' => 'array'
            ],
            [
                'name' => 'estate',
                'type' => 'array',
                'validate' => 'nullable|array'
            ],
            [
                'name' => 'bgColor',
                'type' => 'string',
                'validate' => 'nullable|regex:/^#[abcdef0-9]{6}$/i'
            ],
            [
                'name' => 'template',
                'type' => 'string'
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

        $content['advantages'] = array_values($content['advantages'] ?? []);
        $content['estate']['types'] = array_values(Arr::get($content, 'estate.types', []));

        if (!empty($content['estate']['types'])) {
            $content['estate']['types'] = array_map(static function ($el) {
                $el['id'] = (int) $el['id'];
                $el['active'] = (bool) $el['active'];
                $el['url'] = trim($el['url'] ?? '');

                return $el;
            }, $content['estate']['types']);
        }
        $this->data->put('content', $content);

        $newAttachIds = $this->getAttachIds($this->data);

        if ($oldAttachIds) {
            self::$deleteAttachIds = array_diff($oldAttachIds, $newAttachIds);
        }
    }

    public function setDefaultValue(): void
    {
    }

    protected function getAttachIds(Collection $originData): array
    {
        return NumberHelper::filterInteger(
            array_merge(
                Arr::get($originData, 'content.slider') ?? [],
                [(int)Arr::get($originData, 'content.action.icon', 0)]
            )
        );
    }

    public static function moduleNewsExists(): bool
    {
        return resolve(CoreService::class)->hasModule('news');
    }

    public static function moduleEstateExists(): bool
    {
        return resolve(CoreService::class)->hasModule('estate');
    }
}
