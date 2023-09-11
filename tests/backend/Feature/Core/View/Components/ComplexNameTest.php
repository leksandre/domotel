<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\Core\View\Components;

use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Kelnik\Core\Models\Setting;
use Kelnik\Core\Providers\CoreServiceProvider;
use Kelnik\Core\Repositories\Contracts\SettingsRepository;
use Kelnik\Core\Services\Contracts\SettingsService;
use Kelnik\Core\View\Components\ComplexName;
use Kelnik\Tests\TestCase;

final class ComplexNameTest extends TestCase
{
    use RefreshDatabase;

    public function testShowDefaultNameWhenNameIsNotDefined()
    {
        $component = resolve(ComplexName::class);
        $html = $component->render();

        $this->assertTrue($html === 'Multi.Kelnik');
    }

    public function testShowCorrectComplexNameFromSettings()
    {
        $faker = Factory::create(config('app.faker_locale'));
        $complexName = $faker->company;
        /** @var SettingsService $settingsService */
        $settingsService = resolve(SettingsService::class);
        $setting = new Setting([
            'module' => CoreServiceProvider::MODULE_NAME,
            'name' => $settingsService::PARAM_COMPLEX
        ]);

        $setting->value = ['name' => $complexName];
        resolve(SettingsRepository::class)->set($setting);

        $component = new ComplexName($settingsService);
        $html = $component->render();

        $this->assertTrue($html === $complexName);
    }
}
