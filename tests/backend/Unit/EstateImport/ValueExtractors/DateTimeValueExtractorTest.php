<?php

declare(strict_types=1);

namespace Kelnik\Tests\Unit\EstateImport\ValueExtractors;

use Illuminate\Support\Carbon;
use Kelnik\EstateImport\ValueExtractors\DateTimeValueExtractor;
use Kelnik\Tests\TestCase;

final class DateTimeValueExtractorTest extends TestCase
{
    public function testName()
    {
        $this->assertEquals((new DateTimeValueExtractor())->name(), DateTimeValueExtractor::NAME);
    }

    /** @dataProvider validValueProvider */
    public function testValidExtract($val, $format, $res)
    {
        $this->assertEquals((new DateTimeValueExtractor())($val, $format), $res);
    }

    public static function validValueProvider(): array
    {
        $val1 =  new \DateTime();
        $val2 = '2020-12-12 00:00:00';

        return [
            'Empty string' => ['val' => '', 'format' => null, 'res' => null],
            'DateTime' => [
                'val' => $val1,
                'format' => null,
                'res' => $val1
            ],
            'Date with format' => [
                'val' => $val2,
                'format' => 'Y-m-d H:i:s',
                'res' => Carbon::createFromFormat('Y-m-d H:i:s', $val2)
            ],
        ];
    }
}
