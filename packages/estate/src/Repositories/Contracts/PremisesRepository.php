<?php

declare(strict_types=1);

namespace Kelnik\Estate\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;
use Kelnik\Estate\Models\Contracts\EstateModel;
use Kelnik\Estate\Models\Premises;
use Kelnik\Estate\View\Components\RecommendList\Contracts\Filter;

interface PremisesRepository extends BaseRepository, AdminFilterBySelection
{
    public function findByPrimary(int|string $primary): Premises;

    public function getAdminList(): LengthAwarePaginator;

    public function getAllBySelectionForAdmin(string $selectionClassName): Collection;

    public function getAllBySelectionForAdminPaginated(string $selectionClassName): Paginator;

    public function getByFloorAndSection(array $floorPrimary, int|string $sectionPrimary): Collection;

    /**
     * @param Premises $model
     * @param int[]|null $features
     * @param int[]|null $gallery
     *
     * @return bool
     */
    public function save(EstateModel $model, ?array $features = null, ?array $gallery = null): bool;

    public function saveQuietly(EstateModel $model, ?array $features = null, ?array $gallery = null): bool;

    public function findByPrimaryForCard(int|string $primary): Premises;

    public function loadFullCardData(Premises $premises): Premises;

    public function getRecommends(Filter $filter): Collection;
}
