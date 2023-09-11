<?php

declare(strict_types=1);

namespace Kelnik\Tests\Unit\EstateImport\ValueExtractors;

use Kelnik\EstateImport\ValueExtractors\StringValueExtractor;
use Kelnik\Tests\TestCase;

final class StringValueExtractorTest extends TestCase
{
    public function testName()
    {
        $this->assertEquals((new StringValueExtractor())->name(), StringValueExtractor::NAME);
    }

    /** @dataProvider validValueProvider */
    public function testValidExtract($val, $res)
    {
        $this->assertEquals((new StringValueExtractor())($val), $res);
    }

    public static function validValueProvider(): array
    {
        $val1 = rand(0, 100);

        return [
            'Empty string' => ['val' => '', 'res' => ''],
            'Some string' => ['val' => 'some text', 'res' => 'some text'],
            'Digit' => ['val' => $val1, 'res' => (string)$val1],
        ];
    }
}
