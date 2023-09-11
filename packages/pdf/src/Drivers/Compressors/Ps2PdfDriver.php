<?php

declare(strict_types=1);

namespace Kelnik\Pdf\Drivers\Compressors;

use Kelnik\Pdf\Drivers\Contracts\CompressorDriver;

/**
 * @see https://www.ghostscript.com/doc/current/Use.htm#PDF
 */
final class Ps2PdfDriver extends CompressorDriver
{
    protected function getParams(): array
    {
        return [
            $this->config->binPath,
            '-dPDFSETTINGS=/' . $this->config->level,
            '-',
            '-'
        ];
    }
}
