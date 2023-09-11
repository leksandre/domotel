<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Models\Steps\Contracts;

use Exception;
use Illuminate\Support\Collection;
use Kelnik\Estate\Models\Contracts\EstateModel;
use Kelnik\EstateVisual\Models\Selector;
use Kelnik\EstateVisual\Models\StepElement;
use Kelnik\EstateVisual\Repositories\Contracts\Estate\BaseRepository;
use Kelnik\EstateVisual\Repositories\Contracts\StepElementRepository;

abstract class Step
{
    protected string $name;
    protected ?string $estateModelNamespace = null;

    public function __construct(protected Selector $selector)
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSelector(): Selector
    {
        return $this->selector;
    }

    public function getEstateModelNamespace(): ?string
    {
        return $this->estateModelNamespace;
    }

    abstract public function getPriority(): int;

    abstract public function getTitle(): string;

    abstract public function getAllowedPrev(): array;

    abstract public function getAllowedNext(): array;

    public function getPrevStepName(): ?string
    {
        $steps = array_intersect($this->selector->steps, $this->getAllowedPrev());

        return array_pop($steps);
    }

    public function getSectionTitle(): ?string
    {
        return null;
    }

    public function maskCanLinkToPremises(): bool
    {
        return false;
    }

    public function getNextStepElements(): Collection
    {
        return $this->selector->relationLoaded('stepElements')
            ? $this->selector->stepElements->filter(fn(StepElement $el) => $el->step === $this->getName())
            : new Collection();
    }

    public function adminListAsAccordion(): bool
    {
        return false;
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

        // Add new rows from Estate to EstateVisual list
        $this->getEstateRepository()
            ->getForAdminByComplexPrimary($this->selector->complex_id)
            ->each(function (EstateModel $model) use ($ids, &$res, $stepEstateToElement) {
                if (!in_array($model->getKey(), $ids)) {
                    $el = new StepElement([
                        'estate_model' => $this->estateModelNamespace,
                        'step' => $this->getName(),
                        'title' => $model->title
                    ]);

                    $el->selector()->associate($this->selector);
                    $el->estate_model_id = $model->getKey();
                    $el->modelData = $model->toArray();

                    $this->associateElementParent($el, $this->getPrevStepName(), $stepEstateToElement);

                    $res->add($el);
                }
            });

        return $res;
    }

    public function createStepElement(int|string $estateModelId, int|string|null $parentId): StepElement
    {
        /** @var StepElementRepository $stepElementRepository */
        $stepElementRepository = resolve(StepElementRepository::class);

        $estateEl = $this->findEstateModel($estateModelId);

        if (!$estateEl->exists) {
            throw new Exception(trans('kelnik-estate-visual::admin.errors.estateRowNotFound'));
        }

        $el = new StepElement();
        $el->step = $this->getName();
        $el->selector()->associate($this->selector);
        $el->parent()->associate($parentId);
        $el->estate_model = $estateEl::class;
        $el->estate_model_id = $estateEl->getKey();
        $el->title = $this->prepareTitle($estateEl->title);

        $stepElementRepository->save($el);
        unset($stepElementRepository);

        return $el;
    }

    public function createStepElements(array $prevStepElements, ?Step $prevStep = null): array
    {
        $rows = $this->getEstateElements($prevStepElements)->filter(static fn(StepElement $el) => !$el->exists);

        if (!$rows) {
            return $prevStepElements;
        }

        /** @var StepElementRepository $stepElementRepository */
        $stepElementRepository = resolve(StepElementRepository::class);

        /** @var StepElement $el */
        foreach ($rows as $el) {
            $el->title = $this->prepareTitle($el->title);

            if ($prevStep) {
                $this->associateElementParent($el, $prevStep->getName(), $prevStepElements);
            }

            $el->selector()->associate($this->selector);
            $stepElementRepository->save($el);

            $prevStepElements[$this->getName()][$el->estate_model_id] = $el->getKey();
        }
        unset($stepElementRepository);

        return $prevStepElements;
    }

    abstract public function associateElementParent(
        StepElement &$el,
        ?string $prevStepName,
        array $estateElByStep
    ): void;

    public function prepareTitle(?string $title, ?string $stepName = null): ?string
    {
        $stepName = $stepName ?? $this->getName();

        return is_numeric($title) || (is_string($title) && mb_strlen($title) <= 2)
            ? trans('kelnik-estate-visual::steps.' . $stepName . '.title') . ' ' . $title
            : $title;
    }

    // Data
    //
    abstract public function getEstateRepository(): BaseRepository;

    public function findEstateModel(int|string $primaryKey): EstateModel
    {
        return $this->getEstateRepository()->findByPrimary($primaryKey);
    }

    public function getEstateModelsByPrimary(iterable $primaryKeys): Collection
    {
        return $this->getEstateRepository()->getByPrimary($primaryKeys);
    }

    public function getEstateParentModels(iterable $primaryKeys): Collection
    {
        return $this->getEstateRepository()->getParent($primaryKeys, $this->getPrevStepName());
    }
}
