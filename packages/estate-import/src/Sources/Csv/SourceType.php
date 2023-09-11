<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Sources\Csv;

use Kelnik\EstateImport\Sources\Contracts\SourceType as AbstractSourceType;
use Kelnik\EstateImport\Sources\Csv\Platform\UploadLayout;
use Kelnik\EstateImport\ValueExtractors\DateTimeValueExtractor;
use Kelnik\EstateImport\ValueExtractors\FloatValueExtractor;
use Kelnik\EstateImport\ValueExtractors\IntValueExtractor;
use Kelnik\EstateImport\ValueExtractors\StringValueExtractor;

final class SourceType extends AbstractSourceType
{
    public function getName(): string
    {
        return 'csv';
    }

    public function getPlatformLayouts(): array
    {
        return [UploadLayout::class];
    }

    public function getPreProcessor(): string
    {
        return CsvUploadedFile::class;
    }

    public function getConfig(): array
    {
        return [
            'header' => true,
            'encode' => 'UTF-8',
            'delimiter' => ';',
            'enclosure' => '"',
            'escape' => '\\',
            'cols' => [
                0 => ['class' => StringValueExtractor::class],
                1 => ['class' => IntValueExtractor::class],
                2 => ['class' => FloatValueExtractor::class],
                3 => ['class' => FloatValueExtractor::class],
                4 => ['class' => FloatValueExtractor::class],
                5 => ['class' => IntValueExtractor::class],
                6 => ['class' => StringValueExtractor::class],
                7 => ['class' => FloatValueExtractor::class],
                8 => ['class' => StringValueExtractor::class],
                9 => ['class' => StringValueExtractor::class],
                10 => ['class' => StringValueExtractor::class],
                11 => ['class' => StringValueExtractor::class],
                12 => ['class' => StringValueExtractor::class],
                13 => ['class' => StringValueExtractor::class],
                14 => [
                    'class' => DateTimeValueExtractor::class,
                    'params' => ['format' => 'Y-m-d']
                ],
                15 => ['class' => StringValueExtractor::class],
                16 => ['class' => StringValueExtractor::class],
                17 => ['class' => StringValueExtractor::class],
            ]
        ];
    }

    public function getMapper(): ?string
    {
        return Mapper::class;
    }

    public function runImport(): void
    {
    }
}
