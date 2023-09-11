<?php

declare(strict_types=1);

namespace Kelnik\Core\Repositories\Contracts;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Orchid\Attachment\Models\Attachment;

interface AttachmentRepository
{
    public function findByPrimary(int|string $primary): Attachment;

    public function findByName(string $name): Attachment;

    public function findByNameAndExtension(string $name, string $extension): Attachment;

    public function findByHashAndGroup(string $hash, string $group): Attachment;

    /**
     * @param iterable $primary
     *
     * @return Collection
     */
    public function getByPrimary(iterable $primary): Collection;

    public function getByGroupName(string $groupName): LazyCollection;

    public function delete(Attachment $attachment): ?bool;

    /**
     * @param int|string[] $keys
     *
     * @throws Exception
     */
    public function deleteMass(array $keys): void;
}
