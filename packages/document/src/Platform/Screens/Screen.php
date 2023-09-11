<?php

declare(strict_types=1);

namespace Kelnik\Document\Platform\Screens;

use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Document\Repositories\Contracts\CategoryRepository;
use Kelnik\Document\Repositories\Contracts\GroupRepository;
use Kelnik\Document\Services\Contracts\DocumentService;

abstract class Screen extends \Orchid\Screen\Screen
{
    protected ?string $name = null;

    public function __construct(
        protected CoreService $coreService,
        protected DocumentService $documentService,
        protected GroupRepository $groupRepository,
        protected CategoryRepository $categoryRepository
    ) {
    }
}
