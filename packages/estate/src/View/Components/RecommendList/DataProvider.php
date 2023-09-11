<?php

declare(strict_types=1);

namespace Kelnik\Estate\View\Components\RecommendList;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\Models\PageComponent;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;

final class DataProvider extends ComponentDataProvider
{
    public function getEditLayouts(): array
    {
        return [
            new Layouts\SettingsLayout(),
            Layout::rows([
                Button::make(trans('kelnik-estate::admin.save'))
                    ->icon('bs.save')
                    ->class('btn btn-secondary')
                    ->method('saveData')
            ])
        ];
    }

    public function modifyQuery(array $data): array
    {
        $data['min'] = $this->componentNamespace::COUNT_MIN;
        $data['max'] = $this->componentNamespace::COUNT_MAX;
        $data['default'] = $this->componentNamespace::COUNT_DEFAULT;

        return $data;
    }

    public function getComponentTitle(): string
    {
        return $this->data->get('title') ?? parent::getComponentTitle();
    }

    public function validateSavingRequest(PageComponent $pageComponent, Request $request): void
    {
        $request->validate([
            'data.title' => 'nullable|max:255',
            'data.count' => [
                'required',
                'numeric',
                'between:' . $this->componentNamespace::COUNT_MIN . ',' . $this->componentNamespace::COUNT_MAX
            ],
            'data.template' => 'required|max:150|regex:/[a-z0-9\-_]+/i'
        ]);
    }

    public function setDataFromRequest(PageComponent $pageComponent, Request $request): void
    {
        $this->data = new Collection([
            'title' => $request->input('data.title'),
            'count' => (int)$request->input('data.count'),
            'template' => $request->input('data.template')
        ]);
    }

    public function setDefaultValue(): void
    {
        $this->data = new Collection([
            'title' => $this->getComponentTitleOriginal(),
            'count' => $this->componentNamespace::COUNT_DEFAULT,
            'template' => $this->componentNamespace::getTemplates()->first()?->name
        ]);
    }
}
