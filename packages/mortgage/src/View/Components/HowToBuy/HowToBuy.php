<?php

declare(strict_types=1);

namespace Kelnik\Mortgage\View\Components\HowToBuy;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Kelnik\Core\Helpers\PhoneHelper;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Estate\Services\Contracts\StatService;
use Kelnik\Estate\View\Components\PremisesCard\PremisesCardBufferDto;
use Kelnik\Form\View\Components\Form\FormDto;
use Kelnik\Mortgage\Models\Bank;
use Kelnik\Mortgage\Providers\MortgageServiceProvider;
use Kelnik\Mortgage\Services\Contracts\MortgageService;
use Kelnik\Mortgage\View\Components\Contracts\BaseMortgageCalc;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\Services\Contracts\PageComponentBuffer;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Page\View\Components\Contracts\HasContentAlias;
use Kelnik\Page\View\Components\Contracts\KelnikPageComponent;

final class HowToBuy extends KelnikPageComponent implements HasContentAlias
{
    public const BANKS_VIEW_OFF = 'off';
    public const BANKS_VIEW_LIST = 'list';
    public const BANKS_VIEW_CALC = 'calc';

    private CoreService $coreService;
    private MortgageService $mortgageService;
    private PageService $pageService;
    private PageComponentBuffer $pageComponentBuffer;

    public function __construct()
    {
        $this->coreService = resolve(CoreService::class);
        $this->mortgageService = resolve(MortgageService::class);
        $this->pageService = resolve(PageService::class);
        $this->pageComponentBuffer = resolve(PageComponentBuffer::class);
    }

    public static function getModuleName(): string
    {
        return MortgageServiceProvider::MODULE_NAME;
    }

    public static function getTitle(): string
    {
        return trans('kelnik-mortgage::admin.components.howToBuy.title');
    }

    public static function getAlias(): string
    {
        return 'kelnik-mortgage-how-to-buy';
    }

    public function getContentAlias(): ?string
    {
        return Arr::get($this->getComponentData()->getValue()?->get('content'), 'alias');
    }

    protected function getTemplateData(): array
    {
        $cacheId = $this->getCacheId();
        $res = Cache::get($cacheId);

        if ($res !== null) {
            return $res;
        }

        $content = $this->getComponentData()->getValue()?->get('content');
        $content['template'] = $this->getComponentData()->getValue()?->get('template') ?? '';
        $content['template'] = self::getTemplates()->first(
            static fn(HowToBuyTemplate $el) => $el->name === $content['template']
        ) ?? self::getTemplates()->first();

        /** @var HowToBuyTemplate $template */
        $content['template'] = $content['template']->name;

        $cacheTags = [
            $this->pageService->getPageComponentCacheTag($this->pageComponent->id),
            $this->mortgageService->getBankListCacheTag()
        ];

        $banks = $this->getComponentData()->getValue()->get('banks', []);
        $content['banks'] = $this->mortgageService->getBanksListWithSummary($banks['id'] ?? []);
        $content['showRange'] = !empty($banks['showRange']);
        $content['variants'] = $this->prepareVariants(
            $content['variants'] ?? [],
            $this->getComponentData()->getValue()?->get('calc') ?? [],
            $content['banks']
        );
        $callbackButton = $content['button'] ?? [];
        unset($content['button']);

        $cardInfo = $this->getPremisesCardData();

        if ($cardInfo?->cacheTags) {
            $cacheTags = array_merge($cacheTags, $cardInfo->cacheTags);
        }

        // Callback form
        if (!empty($callbackButton['form_id']) && $this->coreService->hasModule('form')) {
            $formParams = new FormDto();
            $formParams->primary = (int)$callbackButton['form_id'];
            $formParams->pageId = (int)$this->page?->getKey();
            $formParams->pageComponentId = (int)$this->pageComponent?->getKey();
            $formParams->templateData['button_text'] = $callbackButton['text'];
            $formParams->buttonTemplate = 'kelnik-mortgage::components.howToBuy.partials.button';
            $content['callbackForm'] = $formParams;
        }

        $content['calcData'] = $this->makeCalcData($this->getComponentData()->getValue()->get('calc', []));

        Cache::tags(array_unique($cacheTags))->put($cacheId, $content, $this->cacheTtl);

        return $content;
    }

    private function prepareVariants(array $variants, array $calcSettings, Collection $banks): array
    {
        if (!$variants) {
            return $variants;
        }

        $hasEstateModule = $this->coreService->hasModule('estate');
        /** @var ?StatService $estateService */
        $estateStatService = $hasEstateModule ? resolve(StatService::class) : null;
        $calc = $this->setCalcValuesByBankPrograms(new MortgageCalc(), $banks);
        $cardInfo = $this->getPremisesCardData();

        foreach ($variants as $k => &$v) {
            if (empty($v['active'])) {
                unset($variants[$k]);
                continue;
            }

            if (!$hasEstateModule || $v['showBanks'] !== self::BANKS_VIEW_CALC || $banks->isEmpty()) {
                continue;
            }

            $newCalc = clone $calc;
            $v['calc'] = &$newCalc;

            // PremisesCard
            if ($cardInfo?->priceTotal) {
                $newCalc->setMinPrice($cardInfo->priceTotal);
                $newCalc->setMaxPrice($cardInfo->priceTotal);

                $settings = $calcSettings['card'] ?? [];
                $meanTime = (int)($settings['meanTime'] ?? 0);

                $newCalc->setMinFirstPaymentPercent(
                    (int)($settings['firstPayment'] ?? $newCalc::MIN_FIRST_PAYMENT_PERCENT)
                );

                if ($meanTime > 0 && $meanTime <= $newCalc->getMaxTime()) {
                    $newCalc->setMeanTime($meanTime);
                }
                unset($meanTime);

                continue;
            }

            // Base
            $settings = $calcSettings['base'] ?? [];
            $prices = $estateStatService->getEdgePrices();

            $newCalc->setMinPrice($settings['minPrice'] ?? (int)($prices['price_min'] ?? $newCalc::MIN_PRICE));
            $newCalc->setMeanPrice($settings['meanPrice'] ?? null);
            $newCalc->setMaxPrice($settings['maxPrice'] ?? (int)($prices['price_max'] ?? $newCalc::MAX_PRICE));
        }
        unset($v, $newCalc, $calc);

        return $variants;
    }

    private function setCalcValuesByBankPrograms(BaseMortgageCalc $calc, Collection $banks): BaseMortgageCalc
    {
        if ($banks->isEmpty()) {
            return $calc;
        }

        /** @var Bank $bank */
        foreach ($banks as $bank) {
            $calc->setMinFirstPaymentPercent((float)$bank->programsParamRange->get('minPaymentPercent'));
            $calc->setMaxFirstPaymentPercent((float)$bank->programsParamRange->get('maxPaymentPercent'));
            $calc->setMinTime((int)$bank->programsParamRange->get('minTime'));
            $calc->setMaxTime((int)$bank->programsParamRange->get('maxTime'));
        }

        return $calc;
    }

    private function makeCalcData(array $calcData): array
    {
        $fields = ['text', 'helpText', 'phone', 'schedule'];
        $res = [];

        foreach ($fields as $fieldName) {
            $res[$fieldName] = Arr::get($calcData, $fieldName, '');
        }

        $res['phoneLink'] = PhoneHelper::normalize($res['phone'] ?? '');

        if (empty($calcData['buttons'])) {
            return $res;
        }

        foreach (['consult', 'mortgage'] as $k) {
            $v = $calcData['buttons'][$k] ?? [];

            if (!$v) {
                continue;
            }

            $formParams = new FormDto();
            $formParams->primary = (int)$v['form_id'];
            $formParams->pageId = (int)$this->page?->getKey();
            $formParams->pageComponentId = (int)$this->pageComponent?->getKey();
            $formParams->templateData['button_text'] = $v['text'] ?: trans(
                'kelnik-mortgage::front.components.howToBuy.calc.buttons.' . $k
            );
            $formParams->buttonTemplate = 'kelnik-mortgage::components.howToBuy.partials.button-' . $k;

            $res['buttons'][$k] = $formParams;
        }

        return $res;
    }

    public static function initDataProvider(): ComponentDataProvider
    {
        return new DataProvider(self::class);
    }

    public function render(): View|Closure|string|null
    {
        $data = $this->getTemplateData();
        $template = $data['template'];
        unset($data['template']);

        return view($template, $data);
    }

    public static function getTemplates(): Collection
    {
        return new Collection([
            new HowToBuyTemplate(
                'kelnik-mortgage::components.howToBuy.accordion',
                trans('kelnik-mortgage::admin.components.howToBuy.templates.accordion')
            )
        ]);
    }

    private function getPremisesCardData(): ?PremisesCardBufferDto
    {
        return $this->pageComponentBuffer->get(PremisesCardBufferDto::class);
    }

    public function getCacheId(): string
    {
        $cacheId = parent::getCacheId();
        $cardDto = $this->getPremisesCardData();

        if ($cardDto) {
            $cacheId .= '_' . md5(json_encode($cardDto->toArray()));
        }

        return $cacheId;
    }
}
