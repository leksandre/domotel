<?php

declare(strict_types=1);

namespace Kelnik\Pdf\Drivers\Generators;

use Kelnik\Pdf\Drivers\Contracts\GeneratorDriver;
use Symfony\Component\Process\Process;

/**
 * @see https://wkhtmltopdf.org/usage/wkhtmltopdf.txt
 */
final class WkhtmlDriver extends GeneratorDriver
{
    public function printToBinary(string $url): ?string
    {
        $isHtml = !$this->isUrl($url);

        $process = new Process([
            $this->config->binPath,
            '-q',
            '-d', $this->config->dpi,
            '--image-quality', $this->config->imageQuality,
            '-T', $this->config->marginTop,
            '-B', $this->config->marginBottom,
            '-L', $this->config->marginLeft,
            '-R', $this->config->marginRight,
            '-O', ucfirst($this->config->pageOrientation),
            '--page-width', ($this->convertInchToMm($this->config->pageWidth)) . 'mm',
            '--page-height', ($this->convertInchToMm($this->config->pageHeight)) . 'mm',
            $isHtml ? '-' : $url,
            '-'
        ], null, null, $isHtml ? $url : null);
        $process->run();

        if (!$process->isSuccessful()) {
            $this->logError('PDF generator error', $process);
            return null;
        }

        return $process->getOutput();
    }
}
