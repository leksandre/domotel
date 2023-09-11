<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\Core\Providers;

use Kelnik\Tests\TestCase;
use Orchid\Screen\Repository;
use Orchid\Screen\TD;

final class CoreServiceProviderTest extends TestCase
{
    private array $testData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testData = [
            'date' => new Repository(['date' => now()]),
            'color' => new Repository(['color' => '#b0c4de']),
            'stateFalse' => new Repository(['stateFalse' => false]),
            'stateTrue' => new Repository(['stateTrue' => true]),
        ];
    }

    public function testTdHasMacros()
    {
        $this->assertTrue(TD::hasMacro('dateTimeString'));
        $this->assertTrue(TD::hasMacro('colorBlock'));
        $this->assertTrue(TD::hasMacro('booleanState'));
    }

    /**
     * @after testTdHasMacros
     */
    public function testTdMacroDateTimeString()
    {
        $this->refreshApplication();

        $value = $this->testData['date'];
        $html = TD::make('date')->dateTimeString()->buildTd($value)->render();
        $value = $value->get('date')->translatedFormat('d F Y, H:i');
        $this->assertTrue(stripos($html, $value) !== false);

        $model = new TestModel();
        $model->date = $this->testData['date']->get('date');
        $html = TD::make('date')->dateTimeString()->buildTd($model)->render();
        $this->assertTrue(stripos($html, $value) !== false);
    }

    public function testTdMacroColor()
    {
        $this->refreshApplication();

        $value = $this->testData['color'];
        $html = TD::make('color')->colorBlock()->buildTd($value)->render();
        $value = 'background-color: ' . $value->get('color');
        $this->assertTrue(stripos($html, $value) !== false);

        $model = new TestModel();
        $model->color = $this->testData['color']->get('color');
        $html = TD::make('color')->colorBlock()->buildTd($model)->render();
        $this->assertTrue(stripos($html, $value) !== false);
    }

    public function testTdMacroStateTrue()
    {
        $this->refreshApplication();

        $value = $this->testData['stateTrue'];
        $html = TD::make('stateTrue')->booleanState()->buildTd($value)->render();
        $value = 'path="check-circle" componentName="orchid-icon"';
        $this->assertTrue(stripos($html, $value) !== false);

        $model = new TestModel();
        $model->stateTrue = $this->testData['stateTrue']->get('stateTrue');
        $html = TD::make('stateTrue')->booleanState()->buildTd($model)->render();
        $this->assertTrue(stripos($html, $value) !== false);
    }

    public function testTdMacroStateFalse()
    {
        $this->refreshApplication();

        $value = $this->testData['stateFalse'];
        $html = TD::make('stateFalse')->booleanState()->buildTd($value)->render();
        $value = 'path="x-circle" componentName="orchid-icon"';

        $this->assertTrue(stripos($html, $value) !== false);
    }
}
