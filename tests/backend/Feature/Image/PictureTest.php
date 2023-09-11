<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\Image;

use Faker\Factory;
use Faker\Generator;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Kelnik\Image\Config;
use Kelnik\Image\ImageFile;
use Kelnik\Image\Params;
use Kelnik\Image\Picture;
use Kelnik\Tests\TestCase;
use Kelnik\Tests\TestFile;
use Orchid\Attachment\Models\Attachment;

final class PictureTest extends TestCase
{
    use RefreshDatabase;

    private Attachment $attach;
    private Filesystem $storage;
    private Generator $faker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->storage = Storage::fake('public', ['url' => config('filesystems.disks.public.url')]);
        $this->faker = Factory::create(config('app.faker_locale'));

        $image = UploadedFile::fake();
        $image = $image->image('image.jpg', 1920, 1080);

        $image = new TestFile($image);
        $image->setStorage($this->storage);
        $this->attach = $image->load();
    }

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function testInitReturnSameConstructor()
    {
        $image = new ImageFile($this->attach);
        $config = new Config();

        $pictureFromInit = Picture::init($image)->render();
        $pictureFromInitByString = Picture::init($this->attach->name . '.' . $this->attach->extension)->render();
        $pictureFromInitById = Picture::init($this->attach->getKey())->render();
        $pictureFromConstructor = (new Picture($image, $config))->render();

        $this->assertEquals($pictureFromInit, $pictureFromConstructor);
        $this->assertEquals($pictureFromInitByString, $pictureFromConstructor);
        $this->assertEquals($pictureFromInitById, $pictureFromConstructor);
    }

    public function testPictureAttributeAdded()
    {
        $image = new ImageFile($this->attach);

        $cssClass = $this->faker->word;
        $picture = Picture::init($image)->setPictureAttribute('class', $cssClass)->render();

        $this->assertStringContainsString('<picture class="' . $cssClass . '"', $picture);
    }

    public function testSourceAttributeAdded()
    {
        $image = new ImageFile($this->attach);

        $cssClass = $this->faker->word;
        $picture = Picture::init($image);
        $picture->setSourceAttribute('class', $cssClass);

        $this->assertStringContainsString('<source class="' . $cssClass . '"', $picture->render());
    }

    public function testImageAttributeAdded()
    {
        $image = new ImageFile($this->attach);

        $cssClass = $this->faker->word;
        $picture = Picture::init($image);
        $picture->setImageAttribute('class', $cssClass);

        $this->assertStringContainsString('<img class="' . $cssClass . '"', $picture->render());
    }

    public function testCustomBreakpoints()
    {
        $image = new ImageFile($this->attach);
        $config = new Config();

        $breaks = [720 => 960, 320 => 720];
        $picture = Picture::init($image)->setBreakpoints($breaks)->render();

        $cnt = $this->getStringEntriesCount('<source', $picture);

        $breaksCnt = count($breaks);
        $totalFormats = $breaksCnt * count($config->additionalFormats());

        $newFormat = $config->replaceFormats()[$image->getExtension()] ?? false;

        if (!$newFormat || !isset($config->additionalFormats()[$newFormat])) {
            $totalFormats += $breaksCnt;
        }

        $srcset = [];
        $params = new Params();
        $params->filename = $image->getName() . '.' . ($newFormat ?? $image->getExtension());
        $breakPointWidth = current($breaks);
        $addSuffix = count($config->pixelRatio()) > 1;

        foreach ($config->pixelRatio() as $ratio) {
            $params->width = (int)ceil($breakPointWidth * $ratio);
            if ($params->width > $image->getWidth()) {
                continue;
            }
            $srcset[] = Picture::getResizedPath($image, $params) .
                            ($addSuffix && $ratio !== Picture::ORIGINAL_RATIO ? ' ' . $ratio . 'x' : null);
        }
        $srcset = implode(', ', $srcset);

        $this->assertTrue($cnt === $totalFormats);
        $this->assertStringContainsString($srcset, $picture);
    }

    public function testLazyLoadAdded()
    {
        $image = new ImageFile($this->attach);

        $width = rand(16, 150);
        $picture = Picture::init($image)
                    ->setLazyLoad(true)
                    ->setLazyLoadBackgroundWidth($width)
                    ->render();

        $params = new Params($image);
        $params->width = $width;
        $params->blur = true;
        $bgPath = Picture::getResizedPath($image, $params);

        $this->assertStringContainsString("<picture style=\"background-image:url('{$bgPath}');", $picture);
        $this->assertStringContainsString('loading="lazy"', $picture);
        $this->assertStringContainsString(' data-srcset="', $picture);
        $this->assertStringContainsString(' data-src="', $picture);
        $this->assertStringNotContainsString(' srcset="', $picture);
        $this->assertStringNotContainsString(' src="', $picture);
    }

    public function testReplaceOriginFormatForSourceTagIsSuccess()
    {
        $image = new ImageFile($this->attach);
        $pictureReplaced = Picture::init($image)->setLazyLoad(false)->setReplaceFormats(true)->render();
        $pictureNotReplaced = Picture::init($image)->setLazyLoad(false)->setReplaceFormats(false)->render();

        $removeImg = function (string $str) {
            $pos = stripos($str, '<img');

            return substr($str, 0, $pos);
        };

        $pictureReplaced = $removeImg($pictureReplaced);
        $pictureNotReplaced = $removeImg($pictureNotReplaced);

        $this->assertStringContainsString('.jpg', $pictureNotReplaced);
        $this->assertStringNotContainsString('.jpg', $pictureReplaced);
    }

    public function testConstructWithConfigZeroLazyBackgroundWidthUsedConst()
    {
        $image = new ImageFile($this->attach);

        $config = \Mockery::mock(\Kelnik\Image\Contracts\Config::class);
        $config->shouldReceive('lazyLoadBackgroundWidth')->andReturn(0)
            ->shouldReceive('breakpoints')->andReturn([])
            ->shouldReceive('maxWidth')->andReturn(config('kelnik-image.maxWidth'))
            ->shouldReceive('maxHeight')->andReturn(config('kelnik-image.maxHeight'))
            ->shouldReceive('pixelRatio')->andReturn([1])
            ->shouldReceive('replaceFormats')->andReturn([])
            ->shouldReceive('additionalFormats')->andReturn([])
            ->shouldReceive('useOriginalPath')->andReturn(false);

        $picture = (new Picture($image, $config))->setLazyLoad(true)->render();

        $this->assertStringContainsString('/w' . Picture::LAZY_LOAD_BACKGROUND_WIDTH . '/b/', $picture);
    }

    public function testReturnOriginalPathForImgTag()
    {
        $image = new ImageFile($this->attach);

        $config = \Mockery::mock(\Kelnik\Image\Contracts\Config::class);
        $config->shouldReceive('lazyLoadBackgroundWidth')->andReturn(0)
            ->shouldReceive('breakpoints')->andReturn([])
            ->shouldReceive('maxWidth')->andReturn(config('kelnik-image.maxWidth'))
            ->shouldReceive('maxHeight')->andReturn(config('kelnik-image.maxHeight'))
            ->shouldReceive('pixelRatio')->andReturn([1])
            ->shouldReceive('replaceFormats')->andReturn([])
            ->shouldReceive('additionalFormats')->andReturn([])
            ->shouldReceive('useOriginalPath')->andReturn(true)
            ->shouldReceive('useLazyLoad')->andReturn(false);

        $picture = (new Picture($image, $config))->setLazyLoad(false)->render();

        $params = new Params($image);

        $origPath = $image->getUrl();
        $resizerPath = Picture::getResizedPath($image, $params, $config);

        $this->assertStringNotContainsString('<img src="' . $resizerPath . '" ', $picture);
        $this->assertStringContainsString('<img src="' . $origPath . '" ', $picture);
    }

    public function testClosureForAttributesIsSuccess()
    {
        $image = new ImageFile($this->attach);

        $str = 'hello';
        $picture = Picture::init($image)
                    ->setPictureAttribute('data-test', $str)
                    ->setPictureAttribute('class', function (ImageFile $img, array $attrs) {
                        return $img->getWidth() . '_' . $attrs['data-test'];
                    })
            ->render();

        $this->assertStringContainsString(
            '<picture data-test="' . $str . '" class="' . $image->getWidth() . '_' . $str . '"',
            $picture
        );
    }

    public function testOriginFormatExistsInAdditionalAndSourceTagHasNoCopies()
    {
        $image = UploadedFile::fake();
        $image = $image->image('image.webp', 1920, 1080);

        $image = new TestFile($image);
        $image->setStorage($this->storage);

        $attach = $image->load();
        $image = new ImageFile($attach);

        $breaks = [720 => 960, 320 => 720];
        $config = \Mockery::mock(\Kelnik\Image\Contracts\Config::class);
        $config->shouldReceive('lazyLoadBackgroundWidth')->andReturn(0)
            ->shouldReceive('breakpoints')->andReturn($breaks)
            ->shouldReceive('maxWidth')->andReturn(config('kelnik-image.maxWidth'))
            ->shouldReceive('maxHeight')->andReturn(config('kelnik-image.maxHeight'))
            ->shouldReceive('pixelRatio')->andReturn([1])
            ->shouldReceive('replaceFormats')->andReturn([])
            ->shouldReceive('additionalFormats')->andReturn(['webp' => 'image/webp'])
            ->shouldReceive('useOriginalPath')->andReturn(false)
            ->shouldReceive('useLazyLoad')->andReturn(false);

        $picture = (new Picture($image, $config))->render();

        $cnt = $this->getStringEntriesCount('<source', $picture);

        $this->assertTrue($cnt === count($breaks));
    }

    public function testResizedPathWithParamWidthBiggerThanOriginWidthReturnPathToOrigin()
    {
        $image = new ImageFile($this->attach);
        $params = new Params($image);

        $pathOriginWidth = Picture::getResizedPath($image, $params);

        $params->width = $lessWidth = $image->getWidth() - 1;
        $pathLessWidth = Picture::getResizedPath($image, $params);

        $params->width = $biggerWidth = $image->getWidth() + 1;
        $pathBiggerWidth = Picture::getResizedPath($image, $params);

        $this->assertEquals($pathOriginWidth, $pathBiggerWidth);
        $this->assertNotEquals($pathOriginWidth, $pathLessWidth);
        $this->assertNotEquals($pathBiggerWidth, $pathLessWidth);
        $this->assertStringContainsString('/w' . $lessWidth . '/', $pathLessWidth);
        $this->assertStringNotContainsString('/w' . $biggerWidth . '/', $pathBiggerWidth);
    }

    public function testResizedPathWithParamHeightBiggerThanOriginHeightReturnPathToOrigin()
    {
        $image = new ImageFile($this->attach);
        $params = new Params($image);

        $pathOriginHeight = Picture::getResizedPath($image, $params);

        $params->height = $lessHeight = $image->getHeight() - 1;
        $pathLessHeight = Picture::getResizedPath($image, $params);

        $params->height = $biggerWidth = $image->getHeight() + 1;
        $pathBiggerHeight = Picture::getResizedPath($image, $params);

        $this->assertEquals($pathOriginHeight, $pathBiggerHeight);
        $this->assertNotEquals($pathOriginHeight, $pathLessHeight);
        $this->assertNotEquals($pathBiggerHeight, $pathLessHeight);
        $this->assertStringContainsString('/h' . $lessHeight . '/', $pathLessHeight);
        $this->assertStringNotContainsString('/h' . $biggerWidth . '/', $pathBiggerHeight);
    }

    private function getStringEntriesCount(string $needle, string $str)
    {
        $offset = strlen($needle);
        $cnt = [];

        while (($pos = stripos($str, $needle)) !== false) {
            $cnt[] = $pos;
            $tpmOffset = $pos + $offset;
            $str = substr($str, $tpmOffset, strlen($str) - $tpmOffset);
        }
        unset($tmpStr, $pos, $tpmOffset);

        return count($cnt);
    }
}
