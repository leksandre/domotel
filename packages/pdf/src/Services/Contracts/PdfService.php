<?php

declare(strict_types=1);

namespace Kelnik\Pdf\Services\Contracts;

interface PdfService
{
    /**
     * @param string $html
     * @return string Binary data
     */
    public function printToBinary(string $html): string;

    /**
     * @param string $html
     * @return string Base64-encoded string
     */
    public function printToBase64(string $html): string;

    public function printToFile(
        string $moduleName,
        string $filePath,
        string $html,
        array $cacheTags = []
    ): PdfFileResponse;

    public function getFileByPath(string $moduleName, string $filePath): ?PdfFileResponse;
}
