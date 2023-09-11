<?php

declare(strict_types=1);

namespace Kelnik\Estate\View\Components\PremisesCard;

use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Kelnik\Core\Providers\CoreServiceProvider;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Core\Services\Contracts\SettingsService;
use Kelnik\Core\Services\Contracts\SiteService;
use Kelnik\Estate\Models\Premises;
use Kelnik\Estate\Providers\EstateServiceProvider;
use Kelnik\Estate\Services\Contracts\EstateService;
use Kelnik\Form\View\Components\Form\FormDto;
use Kelnik\Image\ImageFile;
use Kelnik\Image\Picture;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\Models\Contracts\RouteProvider;
use Kelnik\Page\Models\Page;
use Kelnik\Page\Models\PageComponent;
use Kelnik\Page\Services\Contracts\PageComponentBuffer;
use Kelnik\Page\Services\Contracts\PageLinkService;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Page\View\Components\Contracts\KelnikPageDynamicComponent;
use Kelnik\Page\View\Components\Contracts\KelnikPageComponent;
use Orchid\Attachment\Models\Attachment;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class PremisesCard extends KelnikPageComponent implements KelnikPageDynamicComponent
{
    use LoadPremises;

    public const BACKGROUND_COLOR = 'color';
    public const BACKGROUND_COLORLESS = 'colorless';

    private ?int $primary = null;
    private ?string $routeName = null;
    private ?Premises $premises = null;
    private readonly CoreService $coreService;
    private readonly EstateService $estateService;
    private readonly PageLinkService $pageLinkService;
    private readonly PageService $pageService;
    private readonly SettingsService $settingsService;

    public function __construct()
    {
        $this->coreService = resolve(CoreService::class);
        $this->estateService = resolve(EstateService::class);
        $this->pageLinkService = resolve(PageLinkService::class);
        $this->pageService = resolve(PageService::class);
        $this->settingsService = resolve(SettingsService::class);
    }

    public static function getModuleName(): string
    {
        return EstateServiceProvider::MODULE_NAME;
    }

    public static function getTitle(): string
    {
        return trans('kelnik-estate::admin.components.premisesCard.title');
    }

    public static function getAlias(): string
    {
        return 'kelnik-estate-premises-card';
    }

    public static function initDataProvider(): ComponentDataProvider
    {
        return new DataProvider(self::class);
    }

    public static function initRouteProvider(Page $page, PageComponent $pageComponent): RouteProvider
    {
        return new \Kelnik\Estate\View\Components\PremisesCard\RouteProvider($page, $pageComponent);
    }

    private function initPdf(): PremisesCardToPdf
    {
        return new PremisesCardToPdf(
            $this->primary,
            Route::current()->getName(),
            $this->pageComponent->getKey(),
            $this->pageComponent->data?->get('pdf') ?? []
        );
    }

    protected function getTemplateData(): array
    {
        $currentRoute = Route::current();
        $this->primary = (int)$currentRoute->parameter(
            \Kelnik\Estate\View\Components\PremisesCard\RouteProvider::PARAM_KEY
        );
        $this->routeName = $currentRoute->getName();
        $cacheId = $this->getCacheId();
        $res = Cache::get($cacheId);

        if ($res !== null) {
            $this->addToBuffer($res);

            return $res;
        }

        $res = [];
        $this->premises = $this->loadPremisesData();

        if (!$this->premises) {
            $this->saveCache($cacheId, $res, $currentRoute);

            return $res;
        }

        $planBreakpoints = [
            1440 => 800,
            1280 => 720,
            960 => 640,
            670 => 773,
            320 => 540
        ];
        $hasImageModule = $this->coreService->hasModule('image');

        if (
            $this->premises->relationLoaded('gallery')
            && $this->premises->gallery->isNotEmpty()
        ) {
            $this->premises->gallery->each(function (Attachment $slide) use ($hasImageModule, $planBreakpoints) {
                $slide->unsetRelation('pivot');
                if ($hasImageModule) {
                    $slide->picture = $this->makePicture($slide, $planBreakpoints);
                }
            });
        }

        $templateName = $this->getComponentData()->getValue()?->get('template') ?? '';

        /** @var Template $template */
        $template = self::getTemplates()->first(
            static fn(Template $el) => $el->name === $templateName
        ) ?? self::getTemplates()->first();

        $this->premises = $this->estateService->preparePremises(
            new Collection([$this->premises]),
            new Collection([$this->premises->type->typeGroup->getKey() => $currentRoute->getName()])
        )->first();

        $hasFloor = $this->premises?->relationLoaded('floor') && $this->premises?->floor->exists;
        $hasBuilding = $hasFloor
            && $this->premises->floor->relationLoaded('building')
            && $this->premises->floor->building->exists;

        if ($hasImageModule) {
            $this->premises->imagePlanPicture = $this->premises->relationLoaded('imagePlan')
                && $this->premises->imagePlan?->exists
                && $this->isNotSvg($this->premises->imagePlan)
                ? $this->makePicture($this->premises->imagePlan, $planBreakpoints)
                : null;

            $this->premises->image3dPicture = $this->premises->relationLoaded('image3D')
                && $this->premises->image3D?->exists
                && $this->isNotSvg($this->premises->image3D)
                ? $this->makePicture($this->premises->image3D, $planBreakpoints)
                : null;

            $this->premises->imageOnFloorPicture = $this->premises->relationLoaded('imageOnFloor')
                && $this->premises->imageOnFloor?->exists
                && $this->isNotSvg($this->premises->imageOnFloor)
                ? $this->makePicture($this->premises->imageOnFloor, $planBreakpoints)
                : null;

            $this->premises->imageBuildingPlanPicture = $hasBuilding
                && $this->premises->floor->building->relationLoaded('complexPlan')
                && $this->premises->floor->building->complexPlan?->exists
                && $this->isNotSvg($this->premises->floor->building->complexPlan)
                ? $this->makePicture($this->premises->floor->building->complexPlan, $planBreakpoints)
                : null;
        }

        $res = [
            'element' => $this->premises,
            'pdfLink' => $this->initPdf()->getLink(),
            'background' => $this->getComponentData()?->getValue()?->get('background') ?? self::BACKGROUND_COLORLESS,
            'currentLink' => url()->current(),
            'template' => $template->name,
            'hasFloor' => $hasFloor,
            'hasBuilding' => $hasBuilding,
            'hasSection' => $hasFloor && $this->premises->relationLoaded('section') && $this->premises->section->exists,

            'hasPop' => $this->hasPop($this->premises),
            'hasPlan' => $this->hasPlan($this->premises),
            'has3dPlan' => $this->has3dPlan($this->premises),
            'hasGallery' => $this->premises->relationLoaded('gallery') && $this->premises->gallery->isNotEmpty(),
            'hasFloorPlan' => $this->premises->relationLoaded('imageOnFloor') && $this->premises->imageOnFloor,
            'hasBuildingPlan' => $hasBuilding
                && $this->premises->floor->building->relationLoaded('complexPlan')
                && $this->premises->floor->building->complexPlan,
            'hasCompletion' => $hasBuilding && $this->premises->floor->building->relationLoaded('completion')
                && $this->premises->floor->building->completion->exists,
            'meta' => $this->getComponentData()?->getValue()?->get('meta')
        ];
        unset($recommendParams, $template);

        if ($res['hasPop']) {
            $colors = $this->settingsService->getCurrentColors();
            $res['planoplanData'] = base64_encode(json_encode([
                'uid' => $this->premises->planoplan_code,
                'primaryColor' => $colors['brand-base'] ?? '#3EB57C',
                'secondaryColor' => '#F4F7F7',
                'textColor' => $colors['brand-text'] ?? '#000000',
                'backgroundColor' => '#FFFFFF'
            ]));
        }

        $res['breadcrumbs'] = $this->pageService->getBreadcrumbs($this->page);
        $res['breadcrumbs'][] = [Str::limit($this->premises->title), url()->current()];

        // Callback form
        $callbackButton = collect($this->getComponentData()->getValue()?->get('callbackButton') ?? []);

        if (!empty($callbackButton['form_id']) && $this->coreService->hasModule('form')) {
            $formParams = new FormDto();
            $formParams->primary = (int)$callbackButton['form_id'];
            $formParams->pageComponentId = (int)$this->pageComponent->getKey();
            $formParams->templateData['button_text'] = $callbackButton['text'];
            $formParams->buttonTemplate = 'kelnik-estate::components.premisesCard.callback-form';
            $res['callbackForm'] = $formParams;
        }

        $res['isVr'] = resolve(SiteService::class)->current()?->type->isVr();
        $res['vr'] = $this->getComponentData()?->get('vr') ?? [];

        $this->addToBuffer($res);
        $this->saveCache($cacheId, $res, $currentRoute);

        return $res;
    }

    private function saveCache(string $cacheId, array $res, \Illuminate\Routing\Route $route): void
    {
        $tags = [
            $this->pageService->getPageComponentCacheTag($this->pageComponent->getKey()),
            $this->pageService->getDynComponentCacheTag($route->getName()),
            $this->estateService->getModuleCacheTag()
        ];

        if (!empty($res['hasPop'])) {
            $tags[] = $this->settingsService->getCacheTag(
                CoreServiceProvider::MODULE_NAME,
                $this->settingsService::PARAM_COLORS
            );
        }

        if ($this->premises?->exists) {
            $tags[] = $this->estateService->getPremisesCacheTag($this->premises->getKey());
        }

        Cache::tags($tags)->put($cacheId, $res, $this->cacheTtl);
    }

    public function render(): View|Closure|string|null
    {
        $data = $this->getTemplateData();

        abort_if(empty($data['element']), Response::HTTP_NOT_FOUND);

        $this->setMeta($data['element'], $data['meta'] ?? null);

        $template = $data['template'];
        $data['backLink'] = url()->previous();
        unset($data['template']);

        if ($data['backLink'] === $data['currentLink']) {
            $data['backLink'] = '/';
        }

        return view($template, $data);
    }

    private function setMeta(Premises $premises, ?array $meta = null): void
    {
        SEOMeta::setTitle($premises->typeShortTitle, false);
        OpenGraph::setTitle($premises->typeShortTitle);

        $image = null;

        if ($this->hasPop($premises)) {
            $image = $premises->planoplan?->widget?->plan();
        }

        if (!$image && $this->hasPlan($premises)) {
            $image = $premises->imagePlan->url();
        } elseif (!$image && $this->has3dPlan($premises)) {
            $image = $premises->image3D->url();
        }

        if ($image) {
            OpenGraph::addImage($image);
        }

        if (!$meta) {
            return;
        }

        /** @var DataProvider $dataProvider */
        $dataProvider = self::initDataProvider();
        $replacement = [];

        foreach ($dataProvider->getReplacementFields() as $el) {
            $val = Arr::get($premises, $el['src']);

            if (!empty($el['callback']) && is_callable($el['callback'])) {
                $val = call_user_func($el['callback'], $val);
            }

            $replacement[$el['var']] = $val ?? '';
        }

        $replacementKeys = array_keys($replacement);
        $replacementVals = array_values($replacement);
        unset($replacement);

        foreach (['title', 'description', 'keywords'] as $tag) {
            if (!isset($meta[$tag])) {
                continue;
            }
            $value = str_replace($replacementKeys, $replacementVals, $meta[$tag]);
            $method = 'set' . ucfirst($tag);

            SEOMeta::{$method}($value);

            if ($tag === 'keywords') {
                continue;
            }

            OpenGraph::{$method}($value);
        }
    }

    private function addToBuffer(array $data): void
    {
        if (empty($data['element'])) {
            return;
        }

        /** @var ?Premises $premises */
        $premises = &$data['element'];

        /** @var PageComponentBuffer $bufferService */
        $bufferService = resolve(PageComponentBuffer::class);
        $buffer = new PremisesCardBufferDto();

        $buffer->elementId = $premises->getKey() ?? 0;
        $buffer->priceTotal = $premises->price_is_visible ? $premises->price_total ?? 0 : 0;
        $buffer->areaTotal = $premises->area_total ?? 0;
        $buffer->typeGroupId = (int)($premises->type?->group_id ?? 0);
        $buffer->typeId = $premises->type?->getKey() ?? 0;
        $buffer->floorId = (int)($premises->floor_id ?? 0);
        $buffer->floorNum = $data['hasFloor'] ? (int)($premises->floor?->number ?? 0) : 0;
        $buffer->sectionId = (int)($premises->section_id ?? 0);
        $buffer->buildingId = (int)($premises->floor->building_id ?? 0);
        $buffer->complexId = $data['hasBuilding'] ? (int)$premises->floor->building?->complex_id : 0;
        $buffer->features = $premises->relationLoaded('features')
            ? $premises->features->pluck('id')->toArray()
            : [];

        $buffer->cacheTags = [
            $this->estateService->getModuleCacheTag(),
            $this->estateService->getPremisesCacheTag($premises->getKey())
        ];

        $buffer->cardRoutes = [
            $premises->type->typeGroup->getKey() => Route::current()->getName()
        ];

        $bufferService->add($buffer);
        unset($buffer);
    }

    private function makePicture(Attachment $attachment, array $breakPoints): ?string
    {
        try {
            return Picture::init(new ImageFile($attachment))->setBreakpoints($breakPoints)->render();
        } catch (Throwable $e) {
        }

        return null;
    }

    private function isNotSvg(Attachment $attachment): bool
    {
        return mb_strtolower($attachment->extension) !== 'svg';
    }

    public static function getTemplates(): Collection
    {
        return new Collection([
            new Template(
                'kelnik-estate::components.premisesCard.residential',
                trans('kelnik-estate::admin.components.premisesCard.templates.residential')
            ),
            new Template(
                'kelnik-estate::components.premisesCard.non-residential',
                trans('kelnik-estate::admin.components.premisesCard.templates.non-residential')
            )
        ]);
    }

    /** @codeCoverageIgnore */
    public static function getBackgroundVariants(): array
    {
        return [
            self::BACKGROUND_COLORLESS => trans('kelnik-estate::admin.components.premisesCard.backgrounds.colorless'),
            self::BACKGROUND_COLOR => trans('kelnik-estate::admin.components.premisesCard.backgrounds.color')
        ];
    }

    public function getCacheId(): string
    {
        return $this->estateService->getPremisesCacheTag(
            'page_' . $this->page->id .
            '_card_' . md5((string)($this->primary ?? ''))
        );
    }

    private function hasPop(Premises $premises): bool
    {
        return mb_strlen($premises->planoplan_code ?? '') > 0;
    }

    private function hasPlan(Premises $premises): bool
    {
        return $premises->relationLoaded('imagePlan') && $premises->imagePlan;
    }

    private function has3dPlan(Premises $premises): bool
    {
        return $premises->relationLoaded('image3D') && $premises->image3D;
    }
}
