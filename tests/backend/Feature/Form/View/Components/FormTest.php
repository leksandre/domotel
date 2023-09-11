<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\Form\View\Components;

use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;
use Illuminate\View\View;
use Kelnik\Form\Models\Form;
use Kelnik\Form\Services\Contracts\FormBaseService;
use Kelnik\Form\Services\FormService;
use Kelnik\Form\View\Components\Form\FormDto;
use Kelnik\Tests\Feature\PageComponentTrait;
use Kelnik\Tests\Feature\SiteTrait;
use Kelnik\Tests\TestCase;
use Mockery;

final class FormTest extends TestCase
{
    use RefreshDatabase;
    use PageComponentTrait;
    use SiteTrait;

    protected function setUp(): void
    {
        parent::setUp();

        $this->initSite();
    }

    private function createForm(array $attributes = []): Form
    {

        if (!isset($attributes['active'])) {
            $attributes['active'] = true;
        }

        return Form::factory()->createOne($attributes);
    }

    private function getStackContent(Component $component, string $stackName): string
    {
        $stackHtml = '';
        $component->render()?->render(function (View $view, ?string $html) use (&$stackHtml, $stackName) {
            $stackHtml = trim($view->getFactory()->yieldPushContent($stackName));
        });

        return $stackHtml;
    }

    public function testActiveFormShowed()
    {
        $form = $this->createForm();

        $componentDto = new FormDto();
        $componentDto->primary = $form->getKey();

        $component = new \Kelnik\Form\View\Components\Form\Form($componentDto);
        $html = $this->getStackContent($component, 'footer');

        $this->assertStringContainsString($form->title, $html);
        $this->assertStringContainsString($form->slug, $html);
    }

    public function testInactiveFormNotShowed()
    {
        $form = $this->createForm(['active' => false]);

        $componentDto = new FormDto();
        $componentDto->primary = $form->getKey();

        $component = new \Kelnik\Form\View\Components\Form\Form($componentDto);
        $html = $this->getStackContent($component, 'footer');

        $this->assertEmpty($html);
    }

    public function testFromHasLinkToPolicyTermsPage()
    {
        $pagePolicy = $this->createPage();
        $form = $this->createForm(['policy_page_id' => $pagePolicy->getKey()]);
        $this->app['router']->getRoutes()->refreshNameLookups();

        $componentDto = new FormDto();
        $componentDto->primary = $form->getKey();
        $componentDto->pageComponentId = rand(1, 1000);
        $componentDto->slug = Factory::create(config('app.faker_locale'))->slug;

        $component = new \Kelnik\Form\View\Components\Form\Form($componentDto);
        $html = $this->getStackContent($component, 'footer');

        $this->assertStringContainsString($form->title, $html);
        $this->assertStringContainsString($componentDto->slug, $html);
        $this->assertStringContainsString($pagePolicy->getUrl(), $html);
    }

    /**     *
     * @param bool $isPassed
     * @param int $primaryValue
     *
     * @return void
     * @dataProvider formPrimaryProvider
     */

    public function testInvalidFormPrimaryKeyShouldReturnEmptyHtml(bool $isPassed, int $primaryValue)
    {
        $componentDto = new FormDto();
        $componentDto->primary = $primaryValue;

        $component = new \Kelnik\Form\View\Components\Form\Form($componentDto);
        $html = $this->getStackContent($component, 'footer');

        $this->assertTrue($isPassed === empty($html));
    }

    public static function formPrimaryProvider(): array
    {
        return [
            'Empty value' => [
                'passed' => true,
                'value' => 0
            ],
            'Missing entry in the database' => [
                'passed' => true,
                'value' => rand(1, 1000)
            ]
        ];
    }

    public function testActiveNewsElementShowedOnPageUsingCache()
    {
        $form = $this->createForm();

        $componentDto = new FormDto();
        $componentDto->primary = $form->getKey();

        $cacheData = (new FormService($componentDto->primary))->build();
        $cacheData['templateData'] = [];
        $cacheData['buttonTemplate'] = $cacheData['slug'] = null;
        $cacheParams = Arr::only($cacheData, ['slug', 'templateData', 'buttonTemplate']);
        $cacheParams['templateData'] = json_encode($cacheParams['templateData']);

        $cacheId = resolve(FormBaseService::class)->getCacheTag(
            $form->getKey() . '_' .
            md5(implode('|', $cacheParams))
        );

        $partialCacheMock = Mockery::mock(Cache::driver())->makePartial();
        $partialCacheMock->shouldReceive('get')->with($cacheId)->andReturn($cacheData);
        Cache::swap($partialCacheMock);

        $component = new \Kelnik\Form\View\Components\Form\Form($componentDto);
        $html = $this->getStackContent($component, 'footer');

        $this->assertStringContainsString(htmlspecialchars($form->title, ENT_QUOTES), $html);
        $this->assertStringContainsString($form->slug, $html);
    }
}
