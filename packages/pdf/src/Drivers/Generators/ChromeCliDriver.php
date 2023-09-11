<?php

declare(strict_types=1);

namespace Kelnik\Pdf\Drivers\Generators;

use Illuminate\Support\Str;
use Kelnik\Pdf\Drivers\Contracts\GeneratorDriver;
use Symfony\Component\Process\Process;

/**
 * @see https://developer.chrome.com/blog/headless-chrome/
 */
final class ChromeCliDriver extends GeneratorDriver
{
    public function printToBinary(string $url): ?string
    {
        $tempResFile = tempnam(sys_get_temp_dir(), 'kelnik-pdf-gen');
        $isHtml = !$this->isUrl($url);

        if ($isHtml) {
            $tempSrcFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'kelnik-pdf-src' . Str::random(8) . '.html';
            file_put_contents($tempSrcFile, $url);
            $url = 'file://' . $tempSrcFile;
        }

        $process = new Process([
            $this->config->binPath,
            '--headless',
            '--disable-gpu',
            '--no-sandbox',
            '--disable-web-security',
            '--hide-scrollbars',
            '--font-render-hinting=' . $this->config->hintingType,
            '--print-to-pdf-no-header',
            '--print-to-pdf=' . $tempResFile,
            $url
        ]);

        $process->run();

        if ($isHtml) {
            unlink($tempSrcFile);
        }

        if (!$process->isSuccessful()) {
            $this->logError('PDF generator error', $process);
            unlink($tempResFile);

            return null;
        }

        $res = file_get_contents($tempResFile);
        unlink($tempResFile);

        return $res;
    }
}
