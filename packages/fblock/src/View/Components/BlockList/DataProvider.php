<?php

declare(strict_types=1);

namespace Kelnik\FBlock\View\Components\BlockList;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Kelnik\FBlock\View\Components\BlockList\Layouts\ContentLayout;
use Kelnik\FBlock\View\Components\BlockList\Layouts\SettingsLayout;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\Models\Contracts\DeleteAttach;
use Kelnik\Page\Models\PageComponent;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;

final class DataProvider extends ComponentDataProvider
{
    use DeleteAttach;

    public function getEditLayouts(): array
    {
        $buttons = Button::make(trans('kelnik-fblock::admin.save'))
                        ->icon('bs.save')
                        ->class('btn btn-secondary')
                        ->method('saveData');

        return [
            Layout::tabs([
                 trans('kelnik-fblock::admin.components.blockList.tabs.content') => new ContentLayout($buttons),
                 trans('kelnik-fblock::admin.components.blockList.tabs.settings') => new SettingsLayout($buttons)
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
                'name' => 'text',
                'type' => 'string',
                'validate' => 'nullable|max:1000'
            ],
            [
                'name' => 'image',
                'type' => 'file'
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

        $validateRules['alias'] = 'nullable|max:150|regex:/[a-z0-9\-_]+/i';
        $validateRules['data.template'] = 'required|max:150|regex:/[a-z0-9\-_]+/i';

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

    protected function getAttachIds(Collection $originData): array
    {
        return array_filter([
            (int)Arr::get($originData, 'content.image')
        ]);
    }
}
