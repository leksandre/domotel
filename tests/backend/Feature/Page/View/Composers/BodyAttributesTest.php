<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\Page\View\Composers;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Kelnik\Core\Models\Enums\Type;
use Kelnik\Core\Models\Site;
use Kelnik\Core\Repositories\Contracts\AttachmentRepository;
use Kelnik\Core\Repositories\Contracts\SettingsRepository;
use Kelnik\Core\Services\Contracts\SettingsService;
use Kelnik\Core\Services\Contracts\SiteService;
use Kelnik\Page\View\Composers\BodyAttributes;
use Kelnik\Tests\TestCase;
use Mockery;

final class BodyAttributesTest extends TestCase
{
    /** @dataProvider bodyAttributeProvider */
    public function testComposerReturnCorrectResult(array $params, $res)
    {
        $viewSpy = Mockery::spy(View::class);

        $composer = new BodyAttributes(
            $this->getSettingsService($params[0]),
            $this->getSiteService($params['site']())
        );
        $composer->compose($viewSpy);

        $viewSpy->shouldHaveReceived('with')->with('bodyAttributes', $res);
    }

    public static function bodyAttributeProvider(): array
    {
        return [
            'Simple and animation is on' => [
                [
                    'site' => fn() => new Site(),
                    ['animation' => ['active' => true]]
                ],
                'class="j-animation"'
            ],
            'Simple and animation is off' => [
                [
                    'site' => fn() => new Site(),
                    ['animation' => ['active' => false]]
                ],
                ''
            ],
            'Touch and animation is on' => [
                [
                    'site' => fn() => new Site(['type' => Type::Touch]),
                    ['animation' => ['active' => true]]
                ],
                'class="j-animation" data-touch="true"'
            ],
            'Touch and animation is off' => [
                [
                    'site' => fn() => new Site(['type' => Type::Touch]),
                    ['animation' => ['active' => false]]
                ],
                'data-touch="true"'
            ]
        ];
    }

    private function getSiteService(Site $site): SiteService
    {
        $stub = $this->getMockBuilder(SiteService::class)->getMock();
        $stub->method('current')->willReturn($site);

        return $stub;
    }

    private function getSettingsService(array $params): SettingsService
    {
        $stub = $this->getMockBuilder(SettingsService::class)
            ->enableOriginalConstructor()
            ->setConstructorArgs([
                'repository' => resolve(SettingsRepository::class),
                'attachmentRepository' => resolve(AttachmentRepository::class)
            ])->getMock();

        $stub->method('getComplex')->willReturn(new Collection($params));

        return $stub;
    }
}
