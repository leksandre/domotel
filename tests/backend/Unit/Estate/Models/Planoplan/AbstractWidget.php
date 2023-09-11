<?php

declare(strict_types=1);

namespace Kelnik\Tests\Unit\Estate\Models\Planoplan;

use Faker\Factory;
use Illuminate\Config\Repository;
use Kelnik\Estate\Models\Planoplan;
use Kelnik\Tests\TestCase;

abstract class AbstractWidget extends TestCase
{
    protected string $className;

    /** @dataProvider widgetData */
    public function testWidgetReturnCorrectValues(array $data, array $result)
    {
        $widget = new $this->className($data);
        $uid = Factory::create()->uuid;

        $this->assertNotFalse(filter_var($widget::dataUrl($uid), FILTER_VALIDATE_URL));
        $this->assertTrue($widget->planTop() === $result['planTop']);
        $this->assertTrue($widget->plan3d() === $result['plan3d']);
        $this->assertTrue($widget->anglePlan3d() === $result['anglePlan3d']);
        $this->assertTrue($widget->plan360() === $result['plan360']);
        $this->assertTrue($widget->qrCodeLink() === $result['qrCodeLink']);
        $this->assertTrue($widget->tourLink() === $result['tourLink']);
    }

    public function testWidgetReturnCorrectPlanUrlByConfig()
    {
        $data = [];
        $widget = new $this->className($data);

        $methods = [
            Planoplan\Contracts\Widget::PLAN_TOP,
            Planoplan\Contracts\Widget::PLAN_3D,
            Planoplan\Contracts\Widget::PLAN_3D_ANGLE
        ];

        $method = $methods[array_rand($methods)];

        $this->mock(Repository::class)
            ->makePartial()
            ->shouldReceive('get')
            ->with('kelnik-estate.planoplan.widget.plan')
            ->andReturn($method);

        $res = $widget->plan();
        $correctRes = $widget->{$method}();

        $this->assertTrue($res === $correctRes);
    }

    public static function widgetData(): array
    {
        $faker = Factory::create();

        $floorId = $faker->unique()->randomDigitNotNull;
        $id = $faker->unique()->randomDigitNotNull;

        $imageTop = $faker->unique()->imageUrl;
        $image3d = $faker->unique()->imageUrl;
        $imageAngle3d = $faker->unique()->imageUrl;

        $tourLink = $faker->unique()->url;
        $qrLink = $faker->unique()->url;

        return [
            'correct data' => [
                'data' => [
                    'designs' => [
                        [
                            'id' => $id,
                            'project' => [
                                'floors_order' => [$floorId],
                                'tree' => [
                                    'plan_' . $floorId . '_' . $id => [
                                        'image_1x' => $imageTop
                                    ],
                                    'top_' . $floorId . '_' . $id => [
                                        'image_1x' => $image3d
                                    ],
                                    'three_quarter_' . $floorId . '_' . $id => [
                                        'image_1x' => $imageAngle3d
                                    ],
                                    'rotate_360_' . $floorId . '_' . $id => [
                                        'image' => []
                                    ],
                                    'virtual_tour' => [
                                        'src' => $tourLink
                                    ]
                                ]
                            ],
                            'qr_code' => $qrLink
                        ]
                    ]
                ],
                'result' => [
                    'planTop' => $imageTop,
                    'plan3d' => $image3d,
                    'anglePlan3d' => $imageAngle3d,
                    'plan360' => [],
                    'qrCodeLink' => $qrLink,
                    'tourLink' => $tourLink
                ]
            ],
            'incorrect data' => [
                'data' => [
                    'designs' => [
                        [
                            'id' => 0,
                            'project' => [
                                'floors_order' => [],
                                'tree' => []
                            ]
                        ]
                    ]
                ],
                'result' => [
                    'planTop' => null,
                    'plan3d' => null,
                    'anglePlan3d' => null,
                    'plan360' => [],
                    'qrCodeLink' => null,
                    'tourLink' => null
                ]
            ]
        ];
    }
}
