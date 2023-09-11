<?php

declare(strict_types=1);

namespace Kelnik\Contact\Platform\Screens;

use Kelnik\Contact\Repositories\Contracts\OfficeRepository;
use Kelnik\Contact\Repositories\Contracts\SocialLinkRepository;
use Kelnik\Contact\Services\ContactService;
use Kelnik\Core\Services\Contracts\CoreService;

abstract class Screen extends \Orchid\Screen\Screen
{
    protected ?string $name = null;

    public function __construct(
        protected CoreService $coreService,
        protected ContactService $contactService,
        protected OfficeRepository $officeRepository,
        protected SocialLinkRepository $socialLinkRepository
    ) {
    }
}
