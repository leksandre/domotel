<?php

declare(strict_types=1);

namespace Kelnik\Pdf\Drivers\Contracts;

use Illuminate\Contracts\Filesystem\Filesystem;
use Kelnik\Pdf\Drivers\Compressors\Config;
use Symfony\Component\Process\Process;

abstract class CompressorDriver extends BaseDriver
{
    protected readonly Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function compressBinary(string $data): string
    {
        $srcStream = fopen('php://temp', 'r+b');
        $tmpStream = fopen('php://temp', 'w+b');

        fwrite($srcStream, $data);
        rewind($srcStream);

        $process = new Process($this->getParams(), null, null, $srcStream);
        $process->disableOutput();
        $process->run(static function (string $type, string $data) use (&$tmpStream) {
            if ($type === Process::OUT) {
                fwrite($tmpStream, $data);
                return;
            }

            $this->errorMsg .= $data;
        });
        fclose($srcStream);

        if (!$process->isSuccessful()) {
            $this->logError('PDF compressor error', $process);
            fclose($tmpStream);

            return $data;
        }

        rewind($tmpStream);

        $res = stream_get_contents($tmpStream);
        fclose($tmpStream);

        return $res;
    }

    public function compressFile(Filesystem $storage, string $srcPath, string $dstPath): bool
    {
        return $this->execute($this->getParams(), $storage, $srcPath, $dstPath);
    }

    protected function execute(array $params, Filesystem $storage, string $srcPath, string $dstPath): bool
    {
        $srcStream = $storage->readStream($srcPath);

        if (!$srcStream) {
            return false;
        }

        $process = new Process($params, null, null, $srcStream);

        $tmpStream = fopen('php://temp', 'wb');
        $process->disableOutput();
        $process->run(function (string $type, string $data) use (&$tmpStream) {
            if ($type === Process::OUT) {
                fwrite($tmpStream, $data);
                return;
            }

            $this->errorMsg .= $data;
        });
        $storage->writeStream($dstPath, $tmpStream);

        fclose($srcStream);
        fclose($tmpStream);

        if (!$process->isSuccessful()) {
            $this->logError('PDF compressor error', $process);

            return false;
        }

        return true;
    }

    abstract protected function getParams(): array;
}
