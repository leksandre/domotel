<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Platform\Screens\StepElement;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Kelnik\EstateVisual\Models\Selector;
use Kelnik\EstateVisual\Models\StepElement;
use Kelnik\EstateVisual\Models\Steps\Factory;
use Kelnik\EstateVisual\Platform\Layouts\StepElement\VisualLayout;
use Kelnik\EstateVisual\Platform\Layouts\StepElement\ElementLayout;
use Kelnik\EstateVisual\Platform\Screens\BaseScreen;
use Kelnik\EstateVisual\Platform\Services\Contracts\SelectorPlatformService;
use Kelnik\EstateVisual\Platform\Services\Contracts\StepElementPlatformService;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layout;
use Symfony\Component\HttpFoundation\Response;

final class EditScreen extends BaseScreen
{
    private bool $exists = false;
    private ?Selector $selector = null;
    private ?StepElement $stepElement = null;
    private StepElementPlatformService $stepElementPlatformService;

    public function __construct()
    {
        parent::__construct();
        $this->stepElementPlatformService = resolve(StepElementPlatformService::class);
    }

    public function query(Selector $selector, StepElement $element): array
    {
        abort_if(!$element->exists || $element->selector_id !== $selector->getKey(), Response::HTTP_NOT_FOUND);

        $step = Factory::make($element->step, $selector);
        $stepNumber = resolve(SelectorPlatformService::class)->getStepNumber($step->getName(), $selector);

        $this->name = trans('kelnik-estate-visual::admin.step') .
            ' ' . $stepNumber .
            '. ' . $step->getTitle() .
            ': ' . $element->title;
        $this->selector = $selector;
        $this->stepElement = $element;

        return [
            'element' => $element,
            'step' => $step,
            'coreService' => $this->coreService
        ];
    }

    /** @return Action[] */
    public function commandBar(): array
    {
        return [
            Link::make(trans('kelnik-estate-visual::admin.back'))
                ->icon('bs.arrow-left-circle')
                ->route(
                    $this->coreService->getFullRouteName('estateVisual.selector.step.list'),
                    $this->stepElement->selector
                ),

            Button::make(trans('kelnik-estate-visual::admin.delete'))
                ->icon('bs.trash3')
                ->method('removeRow')
                ->confirm(trans('kelnik-estate-visual::admin.deleteConfirm', ['title' => $this->name]))
                ->canSee($this->exists),
        ];
    }

    /** @return Layout[] */
    public function layout(): array
    {
        return [
            ElementLayout::class,
            VisualLayout::class,
            \Orchid\Support\Facades\Layout::rows([
                Button::make(trans('kelnik-estate-visual::admin.save'))
                    ->icon('bs.save')
                    ->class('btn btn-secondary')
                    ->method('saveRow')
            ])
        ];
    }

    public function saveRow(Selector $selector, StepElement $element, Request $request): RedirectResponse
    {
        return $this->stepElementPlatformService->save($element, $request);
    }

    public function removeRow(Selector $selector, StepElement $element): RedirectResponse
    {
        return $this->stepElementPlatformService->remove($element);
    }
}
