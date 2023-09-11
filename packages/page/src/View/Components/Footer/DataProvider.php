<?php

declare(strict_types=1);

namespace Kelnik\Page\View\Components\Footer;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\Models\Contracts\DeleteAttach;
use Kelnik\Page\Models\PageComponent;
use Kelnik\Page\View\Components\Footer\Layouts\ContentLayout;
use Kelnik\Page\View\Components\Footer\Layouts\SettingsLayout;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;

final class DataProvider extends ComponentDataProvider
{
    use DeleteAttach;

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
        return new Collection();
    }

    public function getContentFields(): Collection
    {
        return new Collection([
            [
               'name' => 'logo',
               'type' => 'int'
            ],
            [
                'name' => 'link',
                'type' => 'string',
                'validate' => 'nullable|max:150'
            ],
            [
                'name' => 'text',
                'type' => 'string'
            ],
            [
                'name' => 'copyright',
                'type' => 'string',
                'validate' => 'nullable|max:255'
            ],
            [
                'name' => 'policyText',
                'type' => 'string',
                'validate' => 'nullable|max:255'
            ],
            [
                'name' => 'policyLink',
                'type' => 'string',
                'validate' => 'nullable|max:150'
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
        $content['logo'] = (int)$content['logo'];

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
        return array_filter([
            (int)Arr::get($originData, 'content.logo')
        ]);
    }
}
