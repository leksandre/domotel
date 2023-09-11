<?php

declare(strict_types=1);

namespace Kelnik\EstateSearch\View\Components\Search;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\EstateSearch\Models\Contracts\Pagination;
use Kelnik\EstateSearch\Models\Contracts\SearchConfig;
use Kelnik\EstateSearch\Models\Enums\PaginationType;
use Kelnik\EstateSearch\Models\Enums\PaginationViewType;
use Kelnik\EstateSearch\Models\Filters\Contracts\Filter;
use Kelnik\EstateSearch\View\Components\Search\Contracts\HasSearchConfig;
use Kelnik\EstateSearch\View\Components\Search\Layouts\FormLayout;
use Kelnik\EstateSearch\View\Components\Search\Layouts\SearchFormLayout;
use Kelnik\EstateSearch\View\Components\Search\Layouts\SettingsLayout;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\Models\PageComponent;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;

final class DataProvider extends ComponentDataProvider implements HasSearchConfig
{
    public const NO_VALUE = '0';

    public function modifyQuery(array $data): array
    {
        $filters = config('kelnik-estate-search.filters');
        $orders = config('kelnik-estate-search.orders');

        foreach (['filters', 'orders'] as $type) {
            foreach (${$type} as $className) {
                $cl = (new $className());

                if ($cl instanceof Filter && $cl->isHidden()) {
                    continue;
                }

                $data[$type][$className] = $cl->getAdminTitle();
                unset($cl);
            }
            natsort($data[$type]);
        }
        unset($filters, $orders);

        $data['paginationTypes'] = [];
        foreach (PaginationType::cases() as $type) {
            $data['paginationTypes'][$type->value] = $type->title();
        }

        $data['paginationViewTypes'] = [];
        foreach (PaginationViewType::class::cases() as $type) {
            $data['paginationViewTypes'][$type->value] = $type->title();
        }

        return $data;
    }

    public function getEditLayouts(): array
    {
        /** @var CoreService $coreService */
        $coreService = resolve(CoreService::class);
        $buttons = Button::make(trans('kelnik-estate-search::admin.save'))
            ->icon('bs.save')
            ->class('btn btn-secondary')
            ->method('saveData');

        $res = [
            trans('kelnik-estate-search::admin.components.search.tabs.base') => new SettingsLayout($buttons),
            trans('kelnik-estate-search::admin.components.search.tabs.form') => new SearchFormLayout($buttons)
        ];

        if ($coreService->hasModule('form')) {
            $res[trans('kelnik-estate-search::admin.components.search.tabs.callback')] = new FormLayout($buttons);
        }

        return [
            Layout::tabs($res)
        ];
    }

    public function getComponentTitle(): string
    {
        return $this->data->get('title') ?? parent::getComponentTitle();
    }

    public function validateSavingRequest(PageComponent $pageComponent, Request $request): void
    {
        $request->validate([
            'data.title' => 'nullable|max:255',
            'data.types' => 'nullable|array',
            'data.statuses' => 'nullable|array',
            'data.filters' => 'nullable|array',
            'data.orders' => 'nullable|array',
            'data.template' => 'required|max:150|regex:/[a-z0-9\-_]+/i',
            'data.view' => 'required|numeric',
            'data.switch' => 'required|boolean',
            'data.pagination.type' => Rule::enum(PaginationType::class),
            'data.pagination.viewType' => Rule::enum(PaginationViewType::class),
            'data.pagination.perPage' => [
                'required',
                'numeric',
                'min:' . SearchConfig::PAGINATION_PER_PAGE_MIN,
                'max:' . SearchConfig::PAGINATION_PER_PAGE_MAX
            ],
            'data.form_id' => 'nullable|numeric'
        ]);
    }

    public function setDataFromRequest(PageComponent $pageComponent, Request $request): void
    {
        $this->data = new Collection([
            'title' => $request->input('data.title'),
            'types' => array_filter(array_map(
                'intval',
                $request->input('data.types', [])
            )),
            'statuses' => array_filter(array_map(
                'intval',
                $request->input('data.statuses', [])
            )),
            'filters' => $this->uniqueByClass($request->input('data.filters', [])),
            'orders' => $this->uniqueByClass($request->input('data.orders', [])),
            'view' => $request->integer('data.view', Search::VIEW_TYPE_CARD),
            'switch' => $request->boolean('data.switch', true),
            'pagination' => [
                'type' => $request->enum('data.pagination.type', PaginationType::class),
                'viewType' => $request->enum('data.pagination.viewType', PaginationViewType::class),
                'perPage' => $request->integer('data.paginate.perPage', SearchConfig::PAGINATION_PER_PAGE_DEFAULT)
            ],
            'template' => $request->input('data.template'),
            'form_id' => $request->integer('data.form_id')
        ]);
    }

    public function setDefaultValue(): void
    {
        $this->data = new Collection([
            'title' => $this->getComponentTitleOriginal(),
            'template' => $this->componentNamespace::getTemplates()->first()?->name
        ]);
    }

    public function getConfigData(): array
    {
        $res = Arr::only(
            $this->data->toArray(),
            ['types', 'statuses', 'filters', 'orders', 'view', 'switch', 'pagination']
        );
        $res['popup'] = $this->getPopupId();

        $res['pagination']['type'] = PaginationType::tryFrom(
            Arr::get($res, 'pagination.type', '')
        ) ?? PaginationType::getDefault();

        $res['pagination']['viewType'] = PaginationViewType::tryFrom(
            Arr::get($res, 'pagination.viewType', '')
        ) ?? PaginationViewType::getDefault();

        $res['pagination'] = resolve(Pagination::class, $res['pagination']);

        return $res;
    }

    public function getPopupId(): string
    {
        return $this->data->get('form_id')
            ? 'parametric-form-' . $this->data->get('form_id')
            : '';
    }

    private function uniqueByClass(array $data): array
    {
        if (!$data) {
            return $data;
        }

        $cls = [];

        foreach ($data as $k => $v) {
            if (!$v['class'] || isset($cls[$v['class']])) {
                unset($data[$k]);
                continue;
            }

            $cls[$v['class']] = $v['class'];
        }

        return array_values($data);
    }
}
