<?php

declare(strict_types=1);

namespace Kelnik\Tests\Unit\Estate\Models\Planoplan;

use Kelnik\Estate\Models\Planoplan;
use Kelnik\Tests\TestCase;

final class WidgetFactoryTest extends TestCase
{
    /** @dataProvider widgetVersions */
    public function testFactoryReturnCorrectValue(?string $result, Planoplan $planoplan)
    {
        $widget = Planoplan\WidgetFactory::make($planoplan);

        $this->assertTrue($result === null ? $widget === $result : is_a($widget, $result, true));
    }

    public static function widgetVersions(): array
    {
        return [
            'version ' . Planoplan::VERSION_3 => [
                'result' => Planoplan\Contracts\Widget::class,
                'planoplan' => new Planoplan(['version' => Planoplan::VERSION_3])
            ],
            'version ' . Planoplan::VERSION_4 => [
                'result' => Planoplan\Contracts\Widget::class,
                'planoplan' => new Planoplan(['version' => Planoplan::VERSION_4])
            ],
            'random' => [
                'result' => null,
                'planoplan' => new Planoplan([
                    'version' => [
                        rand(1, min(Planoplan::VERSIONS) - 1),
                        rand(max(Planoplan::VERSIONS) + 1, 100)
                    ][rand(0, 1)]
                ])
            ],
        ];
    }
}
