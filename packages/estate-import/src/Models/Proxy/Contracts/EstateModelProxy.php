<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Models\Proxy\Contracts;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Kelnik\Core\Services\Contracts\UploadService;
use Kelnik\Estate\Models\Contracts\EstateModel;
use Kelnik\Estate\Models\Contracts\HasReplacement;
use Kelnik\Estate\Providers\EstateServiceProvider;
use Kelnik\EstateImport\Models\Enums\DataQueueEvent;
use Kelnik\EstateImport\Models\History;
use Kelnik\EstateImport\Repositories\Contracts\AttachmentRepository;
use Kelnik\EstateImport\Repositories\Contracts\BaseLazyCollection;
use Kelnik\EstateImport\Repositories\Contracts\Estate\BaseRepository;
use Kelnik\EstateImport\Services\Contracts\CacheService;
use Orchid\Attachment\Models\Attachment;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mime\MimeTypes;

abstract class EstateModelProxy implements EventState
{
    protected const EVENT_ADDED = 'added';
    protected const EVENT_UPDATED = 'updated';

    protected History $history;
    protected CacheService $cacheService;
    protected LoggerInterface $logger;
    protected Filesystem $storage;

    protected string $namespace;
    protected BaseRepository $repository;
    protected array $data;
    protected ?EstateModel $model;

    protected bool $cached = false;
    protected ?string $event = null;
    protected bool $updateExisting = true;

    /** Save, Update or Delete quietly */
    protected bool $withoutEvents = true;

    abstract public function __construct(
        History $history,
        CacheService $cacheService,
        LoggerInterface $logger,
        Filesystem $storage
    );

    public function setData(array $data): void
    {
        $this->data = $data;

        if (!array_key_exists('hash', $this->data) && $this->hasHashField()) {
            $this->data['hash'] = md5(json_encode($this->data));
        }
    }

    protected function hasHashField(): bool
    {
        return (new $this->namespace())->isFillable('hash');
    }

    public function importBelongs(array &$data, array $belongs): void
    {
        foreach ($belongs as $fieldName => $params) {
            $keyValue = $data[$params['localField']] ?? null;
            unset($data[$params['localField']]);

            if ($keyValue === null) {
                continue;
            }

            $belongModel = $this->getModelRow($params['model'], $keyValue, resolve($params['repository']));

            if (!$belongModel->exists) {
                continue;
            }

            $keyFieldName = $belongModel instanceof HasReplacement
                ? $belongModel->getReplacementField()
                : $belongModel->getKeyName();

            $data[$fieldName] = $belongModel->getAttribute($keyFieldName) ?: $belongModel->getKey();

            if (!empty($params['callback']) && is_callable($params['callback'])) {
                call_user_func($params['callback'], $belongModel);
            }
        }
    }

    public function importAttachments(EstateModel $model, array &$data, array $belongs): void
    {
        if (!$this instanceof BelongsToAttachment) {
            return;
        }

        /** @var UploadService $uploadService */
        $uploadService = resolve(UploadService::class);
        $mimeTypes = new MimeTypes();

        foreach ($belongs as $fieldName => $params) {
            $keyValue = $data[$params['localField']] ?? [];
            unset($data[$params['localField']]);

            if (!$keyValue || !is_array($keyValue)) {
                continue;
            }

            // @TODO: add processing for remote filesystem

            $currentAttachment = $model->getAttribute($fieldName);
            $currentAttachment = $currentAttachment
                ? $this->getModelRow(Attachment::class, $currentAttachment, resolve(AttachmentRepository::class), 'id')
                : false;

            if ($currentAttachment && $currentAttachment->getAttribute('hash') === $keyValue['hash']) {
                continue;
            }

            $filePath = $this->storage->path('');

            if (!str_ends_with($filePath, '/')) {
                $filePath .= '/';
            }

            $filePath .= $keyValue['path'];
            $this->logger->debug('Add new attachment', ['path' => $keyValue['path'], 'file' => $keyValue]);

            $attachment = $uploadService->createAttachment(
                new UploadedFile(
                    $filePath,
                    pathinfo($filePath, PATHINFO_BASENAME),
                    $mimeTypes->guessMimeType($filePath)
                ),
                config('kelnik-estate.storage.disk', 'public'),
                EstateServiceProvider::MODULE_NAME
            );

            if ($attachment->exists) {
                if ($attachment->wasRecentlyCreated) {
                    $this->cacheService->addStat($attachment::class, DataQueueEvent::Added->value);
                }
                $data[$fieldName] = $attachment->getKey();
            }
        }
        unset($mimeTypes, $uploadService);
    }

    public function importRelations(array &$data, array $hasMany): void
    {
        if (!$hasMany) {
            return;
        }

        foreach ($hasMany as $relationName => $relation) {
            if (!method_exists($this->model, $relationName)) {
                $this->logger->error('Model has no relation ' . $relationName, [$relationName, $relation]);
                continue;
            }

            if (empty($data[$relationName])) {
                $this->logger->debug(
                    'Detach rows for relation',
                    [
                        'model' => $this->model,
                        'name' => $relationName,
                        'externalId' => $data['external_id']
                    ]
                );

                $this->repository->removeRelation($this->model->{$relationName}());
                continue;
            }

            if (empty($relation['repository'])) {
                $this->logger->error('Relation has no repository declaration. Skipping.', [$relationName, $relation]);
                continue;
            }

            $rows = $data[$relationName];
            $relationRepository = resolve($relation['repository']);
            $isBelongToMany = $relation instanceof BelongsToMany;

            unset($data[$relationName]);

            foreach ($rows as &$row) {
                $rowIsArray = is_array($row);
                $externalId = $rowIsArray ? ($row['external_id'] ?? '') : mb_strtolower($row);

                $relatedModel = $this->getModelRow($relation['model'], $externalId, $relationRepository);

                if (!$relatedModel->exists) {
                    $relatedModel->setAttribute('title', (string)$row);

                    if ($rowIsArray) {
                        $relatedModel->fill($row);
                    }

                    $relatedModel->setAttribute('external_id', $externalId);

                    $this->logger->debug('Save relation model to DB', [$relatedModel]);
                    $relationRepository->save($relatedModel);
                    $this->cacheService->addModel($relatedModel);
                    $this->cacheService->addStat($relation['proxy'], DataQueueEvent::Added->value);
                }

                $row = $relatedModel;
                unset($relatedModel);
            }
            unset($row);

            $this->logger->debug(
                'Sync row for relation',
                [
                    'model' => $this->model,
                    'name' => $relationName,
                    'externalId' => $data['external_id']
                ]
            );

            $syncRes = $this->repository->syncRelation($this->model->{$relationName}(), new Collection($rows));

            if (!$isBelongToMany || !$syncRes || ($syncRes instanceof Collection && $syncRes->isEmpty())) {
                continue;
            }

            foreach ($syncRes as $el) {
                $this->cacheService->addModel($el);
                $this->cacheService->addStat($relation['proxy'], DataQueueEvent::Added->value);
            }
        }
    }

    public function import(): bool
    {
        if (!$this->data) {
            return false;
        }

        if (!isset($this->data['external_id']) || !mb_strlen((string)$this->data['external_id'])) {
            $this->logger->notice('External ID is empty, skipping', ['data' => $this->data]);

            return false;
        }

        $this->model = $this->getModelRow($this->namespace, $this->data['external_id']);
        $currentHash = $this->model->getAttribute('hash');
        $dataHash = Arr::get($this->data, 'hash');

        if ($this->model->exists && $currentHash && $currentHash === $dataHash) {
            $this->logger->notice(
                'Row hash matched, skipping import',
                [
                    'data' => $this->data,
                    'model' => $this->model->toArray()
                ]
            );

            return false;
        }

        if ($this instanceof BelongsTo) {
            $this->importBelongs($this->data, $this->belongsArr());
        }

        if ($this instanceof BelongsToAttachment) {
            $this->importAttachments($this->model, $this->data, $this->attachmentsArr());
        }

        $hasMany = $this instanceof HasMany;
        $relationNames = $hasMany
            ? array_keys($this->hasManyArr())
            : [];

        foreach ($this->data as $k => $v) {
            if ($hasMany && in_array($k, $relationNames)) {
                continue;
            }
            $this->model->setAttribute($k, $v);
        }

        if (!$this->model->exists || $this->updateExisting) {
            $this->event = $this->model->exists
                ? self::EVENT_UPDATED
                : self::EVENT_ADDED;

            $this->logger->debug('Save model to DB', [$this->model]);
            $method = $this->withoutEvents ? 'saveQuietly' : 'save';
            call_user_func([$this->repository, $method], $this->model);
        }

        if ($hasMany) {
            $this->importRelations($this->data, $this->hasManyArr());
        }

        if (!$this->cached) {
            $this->cacheService->addModel($this->model);
            $this->cached = true;
        }

        return true;
    }

    public function getModelRow(
        string $modelNamespace,
        int|float|string $keyValue,
        ?BaseLazyCollection $repository = null,
        string $keyName = 'external_id'
    ): EstateModel|Attachment {
        if (!$this->cacheService->hasModelList($modelNamespace)) {
            $this->cacheService->cacheModelList($modelNamespace, $repository ?? $this->repository, $keyName);
        }

        $cache = $this->cacheService->getModel($modelNamespace, $keyValue);

        if ($cache) {
            if (!$repository) {
                $this->cached = true;
            }

            return $cache;
        }

        return new $modelNamespace();
    }

    public function isAdded(): bool
    {
        return $this->event === self::EVENT_ADDED;
    }

    public function isUpdated(): bool
    {
        return $this->event === self::EVENT_UPDATED;
    }

    abstract public static function getTitle(): string;

    abstract public static function getSort(): int;
}
