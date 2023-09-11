<?php

declare(strict_types=1);

namespace Kelnik\Pdf\Drivers\Compressors;

use Kelnik\Pdf\Drivers\Contracts\CompressorDriver;

/**
 * @see https://www.ghostscript.com/doc/current/Use.htm#PDF
 */
final class GsDriver extends CompressorDriver
{
    protected function getParams(): array
    {
        return [
            $this->config->binPath,
            '-sstdout=/dev/null',
            '-sDEVICE=pdfwrite',
            '-dTransferFunctionInfo=/Remove',
            '-dPDFSETTINGS=/' . $this->config->level,
            '-dNOPAUSE',
            '-dQUIET',
            '-dBATCH',
            '-sOutputFile=-',
            '-'
        ];
    }
}
