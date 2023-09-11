<?php

declare(strict_types=1);

namespace Kelnik\Tests\Unit\EstateImport\ValueExtractors;

use Kelnik\EstateImport\ValueExtractors\ArrayValueExtractor;
use Kelnik\Tests\TestCase;

final class ArrayValueExtractorTest extends TestCase
{
    public function testName()
    {
        $this->assertEquals((new ArrayValueExtractor())->name(), ArrayValueExtractor::NAME);
    }

    /** @dataProvider validValueProvider */
    public function testValidExtract($val, $callback, $res)
    {
        $this->assertEquals((new ArrayValueExtractor())($val, $callback), $res);
    }

    public static function validValueProvider(): array
    {
        $val1 = rand(0, 100);
        $val2 = [1, 2, 3];
        $callback = static fn($el) => $el * 2;

        return [
            'Empty string' => ['val' => '', 'callback' => null, 'res' => ['']],
            'Some string' => ['val' => 'some text', 'callback' => null, 'res' => ['some text']],
            'Digit' => ['val' => $val1, 'callback' => null, 'res' => [$val1]],
            'Array' => ['val' => $val2, 'callback' => null, 'res' => $val2],
            'Array with callback' => [
                'val' => $val2,
                'callback' => $callback,
                'res' => array_map($callback, $val2)
            ],
        ];
    }
}
