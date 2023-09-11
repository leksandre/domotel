<?php

declare(strict_types=1);

namespace Kelnik\Tests\Unit\EstateImport\ValueExtractors;

use Kelnik\EstateImport\ValueExtractors\IntValueExtractor;
use Kelnik\Tests\TestCase;

final class IntValueExtractorTest extends TestCase
{
    public function testName()
    {
        $this->assertEquals((new IntValueExtractor())->name(), IntValueExtractor::NAME);
    }

    /** @dataProvider validValueProvider */
    public function testValidExtract($val, $res)
    {
        $this->assertEquals((new IntValueExtractor())($val), $res);
    }

    public static function validValueProvider(): array
    {
        for ($i = 1; $i <= 4; $i++) {
            ${'val' . $i} = rand(100, 1000);
        }

        $val3 /= 100;

        return [
            'Zero as string' => ['val' => '0', 'res' => 0],
            'Zero' => ['val' => 0, 'res' => 0],
            'String without digits' => ['val' => 'some text', 'res' => 0],
            'String with digits on ending' => ['val' => 'some text ' . $val1, 'res' => $val1],
            'String with digits on beginning' => ['val' => $val2 . ' and some text', 'res' => $val2],
            'Empty string' => ['val' => '', 'res' => 0],
            'Null' => ['val' => null, 'res' => 0],
            'Float' => ['val' => $val3, 'res' => (int)$val3],
            'Int as string' => ['val' => (string)$val4, 'res' => $val4]
        ];
    }
}
