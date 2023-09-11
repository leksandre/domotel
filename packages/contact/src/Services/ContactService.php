<?php

declare(strict_types=1);

namespace Kelnik\Contact\Services;

use Illuminate\Support\Collection;
use Kelnik\Contact\Dto\ElementSortDto;
use Kelnik\Contact\Models\Office;
use Kelnik\Contact\Models\SocialLink;
use Kelnik\Contact\Repositories\Contracts\OfficeRepository;
use Kelnik\Contact\Repositories\Contracts\SocialLinkRepository;
use Kelnik\Core\Helpers\PhoneHelper;
use Kelnik\Core\Services\Contracts\CoreService;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Field;

final readonly class ContactService implements Contracts\ContactService
{
    public function __construct(
        private CoreService $coreService,
        private OfficeRepository $officeRepository,
        private SocialLinkRepository $socialLinkRepository
    ) {
    }

    public function getOfficeCacheTag(): string
    {
        return self::getCacheTag('office');
    }

    public function getSocialCacheTag(): string
    {
        return self::getCacheTag('social');
    }

    private function getCacheTag(string $suffix): string
    {
        return 'contact_' . $suffix;
    }

    public function getContentLink(): Field
    {
        return Link::make(trans('kelnik-contact::admin.contactElements'))
            ->route(
                $this->coreService->getFullRouteName('contact.office.list')
            )
            ->icon('info')
            ->class('btn btn-info')
            ->target('_blank')
            ->style('display: inline-block; margin-bottom: 20px');
    }

    public function getOffices(): Collection
    {
        $res = $this->officeRepository->getAll();

        if ($res->isEmpty()) {
            return $res;
        }

        return $res->each(function (Office $el) {
            $el->phoneLink = $el->phone ? PhoneHelper::normalize($el->phone) : '';
            $el->append('address');
        });
    }

    public function getSocials(): Collection
    {
        return $this->socialLinkRepository->getAll();
    }

    public function sortOffices(ElementSortDto $dto): bool
    {
        return $this->sortElements(
            $this->officeRepository,
            $dto->elements,
            $dto->defaultPriority
        );
    }

    public function sortSocial(ElementSortDto $dto): bool
    {
        return $this->sortElements(
            $this->socialLinkRepository,
            $dto->elements,
            $dto->defaultPriority
        );
    }

    private function sortElements(
        OfficeRepository|SocialLinkRepository $repo,
        array $elPriority,
        int $defaultPriority
    ): bool {
        $elements = $repo->getAll();

        if ($elements->isEmpty()) {
            return false;
        }

        $elements->each(static function (Office|SocialLink $el) use ($elPriority, $repo, $defaultPriority) {
            $el->priority = (int)array_search($el->getKey(), $elPriority) + $defaultPriority;
            $repo->save($el);
        });

        return true;
    }
}
