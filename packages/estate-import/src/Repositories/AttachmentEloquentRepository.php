<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Repositories;

use Illuminate\Support\LazyCollection;
use Kelnik\Estate\Providers\EstateServiceProvider;
use Kelnik\EstateImport\Repositories\Contracts\AttachmentRepository;
use Orchid\Attachment\Models\Attachment;

final class AttachmentEloquentRepository implements AttachmentRepository
{
    protected string $modelNamespace = Attachment::class;

    public function getLazyCollection(): LazyCollection
    {
        return $this->modelNamespace::select(['id', 'group', 'hash'])
            ->where('group', EstateServiceProvider::MODULE_NAME)
            ->cursor();
    }
}
