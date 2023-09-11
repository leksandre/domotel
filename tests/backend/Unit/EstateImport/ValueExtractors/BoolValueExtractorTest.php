<?php

declare(strict_types=1);

namespace Kelnik\Tests\Unit\EstateImport\ValueExtractors;

use Kelnik\EstateImport\ValueExtractors\BoolValueExtractor;
use Kelnik\Tests\TestCase;

final class BoolValueExtractorTest extends TestCase
{
    public function testName()
    {
        $this->assertEquals((new BoolValueExtractor())->name(), BoolValueExtractor::NAME);
    }

    /** @dataProvider validValueProvider */
    public function testValidExtract($val, $res)
    {
        $this->assertEquals((new BoolValueExtractor())($val), $res);
    }

    public static function validValueProvider(): array
    {
        return [
            'Empty string' => ['val' => '', 'res' => false],
            'Some string' => ['val' => 'some text', 'res' => false],
            'Null' => ['val' => null, 'res' => false],
            'Int' => ['val' => rand(1, 100), 'res' => true],
            'Float' => ['val' => rand(10, 100) / 10, 'res' => true],
            'Zero' => ['val' => 0, 'res' => false],
            'Zero as string' => ['val' => '0', 'res' => false],
            'Y' => ['val' => 'Y', 'res' => true],
            'y' => ['val' => 'y', 'res' => true],
            'yes' => ['val' => 'yes', 'res' => true],
            '+' => ['val' => '+', 'res' => true],
            'да' => ['val' => 'да', 'res' => true],
        ];
    }
}
