<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Services\Contracts;

use Kelnik\Estate\Repositories\Contracts\BaseRepository;
use Orchid\Screen\Field;

interface EstatePlatformService
{
    public function getContentLink(): Field;

    public function sortElements(BaseRepository $repository, array $elPriority, int $defaultPriority): bool;

    public function getElements(): array;
}
