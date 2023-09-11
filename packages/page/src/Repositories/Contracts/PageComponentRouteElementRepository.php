<?php

declare(strict_types=1);

namespace Kelnik\Page\Repositories\Contracts;

use Illuminate\Support\Collection;
use Kelnik\Page\Models\PageComponentRouteElement;

interface PageComponentRouteElementRepository
{
    public function findByPrimary(int|string $primary): PageComponentRouteElement;

    public function getByModelAndElementId(string $modelNameSpace, int|string $elementId): Collection;

    /**
     * $modelElements as associative array [modelName => elementIds]
     * example:
     * [
     *      'ModelNamespace1' => [1, 2],
     *      'ModelNamespace2' => [4]
     * ]
     *
     * @param array[] $modelElements
     *
     * @return Collection
     */
    public function getByModelElements(array $modelElements): Collection;

    public function getByModule(string $moduleName): Collection;

    public function save(PageComponentRouteElement $pageModuleElement): bool;

    public function delete(PageComponentRouteElement $pageModuleElement): bool;

    public function deleteByClassNameAndElement(string $className, int|string $elementId): int;

    public function deleteBySiteClassNameAndElement(int|string $siteId, string $className, int|string $elementId): int;

    public function deleteByModule(string $moduleName): int;
}
