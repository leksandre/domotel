<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Platform\Layouts\Selector;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\EstateVisual\Models\StepElement;
use Kelnik\EstateVisual\Models\Steps\Contracts\Step;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layout;
use Orchid\Screen\Repository;
use Orchid\Screen\TD;

final class StepListLayout extends Layout
{
    protected $template = 'kelnik-estate-visual::platform.layouts.step';

    public function __construct(
        private Step $step,
        private Collection $estateElements,
        private ?Collection $groups = null
    ) {
    }

    public function build(Repository $repository)
    {
        /** @var CoreService $coreService */
        $coreService = $repository->get('coreService');

        $columns = new Collection([
           TD::make('title')
               ->width('50%')
               ->render(static function (StepElement $el) use ($coreService) {
                   return $el->exists
                       ? Link::make($el->title)
                           ->route(
                               $coreService->getFullRouteName('estateVisual.selector.step.edit'),
                               [
                                   'selector' => $el->selector_id,
                                   'element' => $el->getKey()
                               ]
                           )
                           ->style('font-weight:bold')
                       : $el->title;
               }),
           TD::make('info')
                ->width('50%')
                ->render(static function (StepElement $el) {
                    return $el->exists
                        ? trans('kelnik-estate-visual::admin.updated') . ': ' .
                            $el->updated_at->translatedFormat('d F Y, H:i')
                        : Button::make('kelnik-estate-visual::admin.add')
                            ->class('btn btn-secondary')
                            ->icon('bs.plus-circle')
                            ->method('addStepElement')
                            ->parameters([
                                'selector' => $el->selector_id,
                                'step' => Crypt::encryptString($el->step),
                                'model_id' => $el->estate_model_id,
                                'parent_id' => $el->parent_id
                            ]);
                })
        ]);

        return view($this->template, [
            'step' => $this->step->getName(),
            'columns' => $columns,
            'rows' => $this->estateElements,
            'title' => $this->title,
            'accordion' => $this->step->adminListAsAccordion(),
            'groups' => $this->groups
        ]);
    }

    public function title(string $title = null): self
    {
        $this->title = $title;

        return $this;
    }
}
