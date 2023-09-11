<?php

declare(strict_types=1);

namespace Kelnik\Estate\View\Components\PremisesCard;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Kelnik\Core\Helpers\NumberHelper;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Estate\View\Components\PremisesCard\Layouts\FormLayout;
use Kelnik\Estate\View\Components\PremisesCard\Layouts\MetaLayout;
use Kelnik\Estate\View\Components\PremisesCard\Layouts\PdfLayout;
use Kelnik\Estate\View\Components\PremisesCard\Layouts\RoutesLayout;
use Kelnik\Estate\View\Components\PremisesCard\Layouts\SettingsLayout;
use Kelnik\Estate\View\Components\PremisesCard\Layouts\VrLayout;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\Models\Contracts\DeleteAttach;
use Kelnik\Page\Models\PageComponent;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;

final class DataProvider extends ComponentDataProvider
{
    use DeleteAttach;

    public const PHONE_REGEXP = '[0-9()\-+ ]+';
    public const PHONE_LIMIT = 50;
    public const PHONE_MAX_CNT = 2;
    public const SCHEDULE_LIMIT = 100;
    public const SCHEDULE_MAX_CNT = 3;
    public const ABOUT_TITLE_LIMIT = 150;
    public const ABOUT_ADDRESS_LIMIT = 150;
    public const ABOUT_IMAGES_LIMIT = 4;
    public const ABOUT_TEXT_LIMIT = 500;
    public const UTP_TITLE_LIMIT = 100;
    public const UTP_TEXT_LIMIT = 100;
    public const UTP_CNT_MAX = 10;
    public const BUTTON_TEXT_LIMIT = 100;
    public const NO_VALUE = '0';
    public const META_LIMIT = 255;

    public function modifyQuery(array $data): array
    {
        $data['replacement'] = $this->getReplacementFields();

        return $data;
    }

    public function getEditLayouts(): array
    {
        $buttons = Button::make(trans('kelnik-estate::admin.save'))
                        ->icon('bs.save')
                        ->class('btn btn-secondary')
                        ->method('saveData');

        /** @var CoreService $coreService */
        $coreService = resolve(CoreService::class);

        $res = [
            trans('kelnik-estate::admin.components.premisesCard.settings.title') => new SettingsLayout($buttons),
            trans('kelnik-estate::admin.components.premisesCard.meta.title') => new MetaLayout($buttons),
            trans('kelnik-estate::admin.components.premisesCard.route.title') => new RoutesLayout($buttons),
            trans('kelnik-estate::admin.components.premisesCard.vr.title') => new VrLayout($buttons)
        ];

        if ($coreService->hasModule('form')) {
            $res[trans('kelnik-estate::admin.components.premisesCard.callback.title')] = new FormLayout($buttons);
        }

        if ($coreService->hasModule('pdf')) {
            $res[trans('kelnik-estate::admin.components.premisesCard.pdf.title')] = new PdfLayout($buttons);
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
            'data.template' => 'required|max:150|regex:/[a-z0-9\-_]+/i',
            'data.background' => 'nullable|in:' . implode(',', array_keys(PremisesCard::getBackgroundVariants())),
            'data.callbackButton.text' => 'nullable|max:' . self::BUTTON_TEXT_LIMIT,
            'data.callbackButton.form_id' => 'nullable|numeric',
            'data.vr.buttonText' => 'nullable|max:' . self::BUTTON_TEXT_LIMIT,
            'data.pdf.phone.*.value' => 'nullable|max:' . self::PHONE_LIMIT . '|regex:/' . self::PHONE_REGEXP . '/i',
            'data.pdf.schedule.*.value' => 'nullable|max:' . self::SCHEDULE_LIMIT,
            'data.pdf.about.title' => 'nullable|max:' . self::ABOUT_TITLE_LIMIT,
            'data.pdf.about.address' => 'nullable|max:' . self::ABOUT_ADDRESS_LIMIT,
            'data.pdf.about.text' => 'nullable|max:' . self::ABOUT_TEXT_LIMIT,
            'data.pdf.utp.*.title' => 'nullable|max:' . self::UTP_TITLE_LIMIT,
            'data.pdf.utp.*.text' => 'nullable|max:' . self::UTP_TEXT_LIMIT,
            'data.meta' => 'nullable|array',
            'data.meta.title' => 'nullable|max:' . self::META_LIMIT,
            'data.meta.description' => 'nullable|max:' . self::META_LIMIT,
            'data.meta.keywords' => 'nullable|max:' . self::META_LIMIT
        ]);
    }

    public function setDataFromRequest(PageComponent $pageComponent, Request $request): void
    {
        $data = [
            'title' => $request->input('data.title'),
            'template' => $request->input('data.template'),
            'background' => $request->input('data.background'),
            'callbackButton' => $request->input('data.callbackButton'),
            'vr' => $request->input('data.vr'),
            'pdf' => $request->input('data.pdf'),
            'meta' => $request->input('data.meta')
        ];
        $data['callbackButton']['form_id'] = (int)($data['callbackButton']['form_id'] ?? self::NO_VALUE);

        foreach (['phone', 'schedule'] as $section) {
            $data['pdf'][$section] = array_values(
                array_filter($data['pdf'][$section] ?? [], static fn($el) => Str::length($el['value']))
            );
        }

        $data['pdf']['about']['images'] = array_values(
            array_map('intval', $data['pdf']['about']['images'] ?? [])
        );

        $data['pdf']['about']['utp'] = array_values(
            array_filter($data['pdf']['about']['utp'] ?? [], static fn($el) => Str::length($el['text']))
        );

        $oldAttachIds = $this->getAttachIds($this->data);
        $this->data = collect($data);
        unset($data);

        $newAttachIds = $this->getAttachIds($this->data);

        if ($oldAttachIds) {
            self::$deleteAttachIds = array_diff($oldAttachIds, $newAttachIds);
        }
    }

    public function setDefaultValue(): void
    {
        $this->data = new Collection([
            'title' => $this->getComponentTitleOriginal(),
            'template' => $this->componentNamespace::getTemplates()->first()?->name,
            'background' => PremisesCard::BACKGROUND_COLORLESS
        ]);
    }

    /** @return array<string, array{src:string, var:string, title:string, callback:?Closure}> */
    public function getReplacementFields(): array
    {
        $fields = [
            'areaTotal' => [
                'src' => 'area_total',
                'callback' => fn($value) => trans(
                    'kelnik-estate::front.components.premisesCard.properties.area',
                    ['value' => $value]
                )
            ],
            'areaLiving' => [
                'src' => 'area_living',
                'callback' => fn($value) => trans(
                    'kelnik-estate::front.components.premisesCard.properties.area',
                    ['value' => $value]
                )
            ],
            'priceTotal' => [
                'src' => 'price_total',
                'callback' => fn($value) => trans(
                    'kelnik-estate::front.components.premisesCard.properties.price',
                    ['value' => number_format($value, 0, ',', ' ')]
                )
            ],
            'rooms' => ['src' => 'rooms'],
            'name' => ['src' => 'title'],
            'number' => ['src' => 'number'],
            'numberOnFloor' => ['src' => 'number_on_floor'],
            'typeName' => ['src' => 'type.title'],
            'statusName' => ['src' => 'status.title'],
            'floorName' => ['src' => 'floor.title'],
            'floorNumber' => ['src' => 'floor.number'],
            'sectionName' => ['src' => 'section.title'],
            'buildingName' => ['src' => 'floor.building.title'],
            'complexName' => ['src' => 'floor.building.complex.title'],
            'features' => [
                'src' => 'features',
                'callback' => fn(?Collection $value) => $value?->implode('title', ', ') ?? ''
            ]
        ];

        $res = [];

        foreach ($fields as $fieldName => $field) {
            $res[$fieldName] = [
                'src' => $field['src'],
                'var' => '{' . $fieldName . '}',
                'title' => trans('kelnik-estate::admin.components.premisesCard.replacement.fields.' . $field['src']),
                'callback' => $field['callback'] ?? null
            ];
        }

        ksort($res);

        return $res;
    }

    protected function getAttachIds(Collection $originData): array
    {
        return NumberHelper::filterInteger(Arr::get($originData, 'pdf.about.images') ?? []);
    }
}
