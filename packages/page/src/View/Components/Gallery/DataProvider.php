<?php

declare(strict_types=1);

namespace Kelnik\Page\View\Components\Gallery;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Kelnik\Core\Helpers\NumberHelper;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\Models\Contracts\DeleteAttach;
use Kelnik\Page\Models\PageComponent;
use Kelnik\Page\View\Components\Gallery\Layouts\ContentLayout;
use Kelnik\Page\View\Components\Gallery\Layouts\SettingsLayout;
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
                'name' => 'slider',
                'type' => 'array',
                'validate' => 'nullable|array'
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
        $this->data->put('content', $content);

        $newAttachIds = $this->getAttachIds($this->data);

        if ($oldAttachIds) {
            self::$deleteAttachIds = array_diff($oldAttachIds, $newAttachIds);
        }
    }

    public function setDefaultValue(): void
    {
        $this->data = new Collection([
            'content' => [
                'title' => $this->getComponentTitleOriginal()
            ]
        ]);
    }

    protected function getAttachIds(Collection $originData): array
    {
        return NumberHelper::filterInteger(
            Arr::get($originData, 'content.slider') ?? []
        );
    }
}
