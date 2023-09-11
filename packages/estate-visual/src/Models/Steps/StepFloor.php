<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Models\Steps;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Kelnik\Estate\Models\Contracts\EstateModel;
use Kelnik\Estate\Models\Floor;
use Kelnik\EstateVisual\Models\StepElement;
use Kelnik\EstateVisual\Models\Steps\Contracts\Step;
use Kelnik\EstateVisual\Repositories\Contracts\Estate\FloorRepository;

final class StepFloor extends Step
{
    protected string $name = Factory::STEP_FLOOR;
    protected ?string $estateModelNamespace = Floor::class;

    public function getPriority(): int
    {
        return 30;
    }

    public function getTitle(): string
    {
        return trans('kelnik-estate-visual::steps.floor.title');
    }

    public function getAllowedPrev(): array
    {
        return [
            Factory::STEP_COMPLEX,
            Factory::STEP_BUILDING,
            Factory::STEP_SECTION
        ];
    }

    public function getAllowedNext(): array
    {
        return [];
    }

    public function getSectionTitle(): ?string
    {
        return trans('kelnik-estate-visual::steps.building.title');
    }

    public function getEstateElements(array &$stepEstateToElement): Collection
    {
        $res = $this->getNextStepElements();
        $ids = [];

        /** @var StepElement $el */
        foreach ($res as $el) {
            $stepEstateToElement[$el->step][$el->estate_model_id] = $el->getKey();
            $ids[] = $el->estate_model_id;
        }

        $prevStepIsSection = $this->getPrevStepName() === Factory::STEP_SECTION;

        // Add new rows from Estate to EstateVisual list
        $this->getEstateRepository()
            ->getForAdminByComplexPrimary($this->selector->complex_id)
            ->each(function (EstateModel $model) use ($ids, &$res, $stepEstateToElement, $prevStepIsSection) {
                if (!in_array($model->getKey(), $ids)) {
                    $el = new StepElement([
                        'estate_model' => $this->estateModelNamespace,
                        'step' => $this->getName(),
                        'title' => $model->title
                    ]);

                    $el->selector()->associate($this->selector);
                    $el->estate_model_id = $model->getKey();
                    $el->modelData = $model->toArray();

                    if ($prevStepIsSection) {
                        foreach ($model->getAttribute('section_id') as $sectionId) {
                            $elCopy = $this->replicateStepElement($el);
                            $elCopy->modelData['section_id'] = $sectionId;
                            $this->associateElementParent($elCopy, $this->getPrevStepName(), $stepEstateToElement);

                            $res->add($elCopy);
                        }

                        return;
                    }

                    $this->associateElementParent($el, $this->getPrevStepName(), $stepEstateToElement);

                    $res->add($el);
                }
            });

        return $res;
    }

    private function replicateStepElement(StepElement $el): StepElement
    {
        $elCopy = $el->replicate();
        $elCopy->modelData = $el->modelData;

        return $elCopy;
    }

    public function associateElementParent(StepElement &$el, ?string $prevStepName, array $estateElByStep): void
    {
        $stepName = Factory::STEP_BUILDING;
        $column = 'building_id';

        if ($prevStepName === Factory::STEP_SECTION) {
            $stepName = $prevStepName;
            $column = 'section_id';
        } elseif ($prevStepName === Factory::STEP_COMPLEX) {
            $stepName = $prevStepName;
            $column = 'complex_id';
        }

        if (empty($el->modelData[$column])) {
            return;
        }

        $el->parent()->associate(
            Arr::get($estateElByStep, $stepName . '.' . $el->modelData[$column], 0)
        );
    }

    public function maskCanLinkToPremises(): bool
    {
        return true;
    }

    public function getEstateRepository(): FloorRepository
    {
        return resolve(FloorRepository::class);
    }

    public function adminListAsAccordion(): bool
    {
        return true;
    }

    public function getEstateParentModels(iterable $primaryKeys): Collection
    {
        return $this->getEstateRepository()->getParent(
            $primaryKeys,
            $this->getPrevStepName() ?? Factory::STEP_BUILDING
        );
    }
}
