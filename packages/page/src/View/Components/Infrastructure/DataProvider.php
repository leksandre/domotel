<?php

declare(strict_types=1);

namespace Kelnik\Page\View\Components\Infrastructure;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Kelnik\Core\Helpers\NumberHelper;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\Models\Contracts\DeleteAttach;
use Kelnik\Page\Models\PageComponent;
use Kelnik\Page\View\Components\Infrastructure\Layouts\ContentLayout;
use Kelnik\Page\View\Components\Infrastructure\Layouts\SettingsLayout;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;

final class DataProvider extends ComponentDataProvider
{
    use DeleteAttach;

    public const TEXT_LIMIT = 400;
    public const UTP_LIMIT = 20;

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
               'name' => 'text',
               'type' => 'string',
               'validate' => 'nullable|max:' . self::TEXT_LIMIT
           ],
           [
               'name' => 'legend',
               'type' => 'array',
               'validate' => 'nullable|array'
           ],
           [
               'name' => 'plan',
               'type' => 'file',
               'validate' => 'nullable|numeric'
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
        $newAttachIds = [];

        foreach ($this->getContentFields() as $field) {
            $content[$field['name']] = $request->input('data.content.' . $field['name']);
        }

        if (isset($content['legend'])) {
            $content['legend'] = array_map(static function ($el) use (&$newAttachIds) {
                $el['icon'] = (int)$el['icon'];
                $newAttachIds[$el['icon']] = $el['icon'];

                return $el;
            }, $content['legend']);
            $content['legend'] = array_values($content['legend']);
        }

        if ($content['plan']) {
            $newAttachIds[(int)$content['plan']] = (int)$content['plan'];
        }

        $this->data->put('content', $content);

        if ($oldAttachIds) {
            self::$deleteAttachIds = array_diff($oldAttachIds, array_values($newAttachIds));
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
            array_merge(
                Arr::pluck(Arr::get($originData, 'content.legend') ?? [], 'icon'),
                [(int)Arr::get($originData, 'content.plan')]
            )
        );
    }
}
