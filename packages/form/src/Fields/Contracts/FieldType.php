<?php

declare(strict_types=1);

namespace Kelnik\Form\Fields\Contracts;

use Symfony\Component\HttpFoundation\FileBag;

interface FieldType
{
    public function __construct(
        string $formName,
        string $name,
        string $title,
        array $params = []
    );

    public function validate(array $data, FileBag $files): bool|array;

    public function process(array $data, FileBag $files): string;

    public static function initDataProvider(): FieldDataProvider;

    public static function getTypeTitle(): string;

    public function isRequired(): bool;
}
