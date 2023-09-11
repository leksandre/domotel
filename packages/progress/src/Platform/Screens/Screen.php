<?php

declare(strict_types=1);

namespace Kelnik\Progress\Platform\Screens;

use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Progress\Repositories\Contracts\AlbumRepository;
use Kelnik\Progress\Repositories\Contracts\CameraRepository;
use Kelnik\Progress\Repositories\Contracts\GroupRepository;
use Kelnik\Progress\Services\Contracts\ProgressService;

abstract class Screen extends \Orchid\Screen\Screen
{
    protected ?string $name = null;

    public function __construct(
        protected CoreService $coreService,
        protected AlbumRepository $albumRepository,
        protected ProgressService $progressService,
        protected CameraRepository $cameraRepository,
        protected GroupRepository $groupRepository
    ) {
    }
}
