<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\View\Components\Contracts;

use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Estate\Models\PremisesTypeGroup;
use Kelnik\Estate\Repositories\Contracts\PremisesTypeGroupRepository;
use Kelnik\Estate\Services\Contracts\EstateService;
use Kelnik\EstateVisual\Models\Selector;
use Kelnik\EstateVisual\Models\Steps\Factory;
use Kelnik\EstateVisual\Providers\EstateVisualServiceProvider;
use Kelnik\EstateVisual\Repositories\Contracts\StepElementRepository;
use Kelnik\Form\View\Components\Form\FormDto;
use Kelnik\Page\View\Components\Contracts\KelnikPageComponent;

abstract class AbstractSelector extends KelnikPageComponent
{
    protected const CACHE_TTL = 864000; // 10 days

    protected readonly EstateService $estateService;
    protected readonly CoreService $coreService;

    public function __construct()
    {
        $this->coreService = resolve(CoreService::class);
        $this->estateService = resolve(EstateService::class);
    }

    public static function getModuleName(): string
    {
        return EstateVisualServiceProvider::MODULE_NAME;
    }

    public static function getPlural(array $types): string
    {
        $plural = explode('|', trans('kelnik-estate-visual::front.plural.premises'));

        if (count($types) === 1) {
            /** @var PremisesTypeGroup $type */
            $type = resolve(PremisesTypeGroupRepository::class)->findByPrimary(current($types));
            if ($type->exists && $type->plural) {
                $plural = $type->plural;
            }
            unset($type);
        }

        return base64_encode(json_encode($plural));
    }

    protected function getCallbackForm(array $callbackForm): ?FormDto
    {
        if (empty($callbackForm['id']) || !$this->coreService->hasModule('form')) {
            return null;
        }

        $formParams = new FormDto();
        $formParams->primary = (int)$callbackForm['id'];
        $formParams->templateData['button_text'] = $callbackForm['text'];
        $formParams->template = 'kelnik-estate-visual::form.booking';

        return $formParams;
    }

    protected function getStepInfo(Selector $selector): array
    {
        $firstStep = current($selector->steps);

        return $firstStep !== Factory::STEP_COMPLEX
            ? [
                'name' => $firstStep,
                'id' => resolve(StepElementRepository::class)
                    ->getFirstStepBySelector($selector->getKey(), $firstStep)
                    ->getKey()
            ]
            : [];
    }
}
