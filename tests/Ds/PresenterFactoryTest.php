<?php
declare(strict_types = 1);
namespace Tests\App\Ds;

use App\BlogsService\Domain\Image;
use App\Ds\Molecule\Image\ImagePresenter;
use App\Ds\PresenterFactory;
use App\Translate\TranslateProvider;
use PHPUnit\Framework\TestCase;
use RMP\Translate\Translate;

/**
 * @covers App\Ds\PresenterFactory
 */
class PresenterFactoryTest extends TestCase
{
    /** @var PresenterFactory */
    private $factory;

    public function setUp()
    {
        $translateProvider = $this->createMock(TranslateProvider::class);
        $translateProvider->method('getTranslate')->willReturn($this->createMock(Translate::class));
        $this->factory = new PresenterFactory($translateProvider);
    }

    public function testImage()
    {
        $mockImage = $this->createMock(Image::class);

        $this->assertEquals(
            new ImagePresenter($mockImage, 240, '1'),
            $this->factory->imagePresenter($mockImage, 240, '1')
        );
    }
}
