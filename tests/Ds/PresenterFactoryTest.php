<?php
declare(strict_types = 1);
namespace Tests\App\Ds;

use App\BlogsService\Domain\Image;
use App\Ds\Molecule\Image\ImagePresenter;
use App\Ds\PresenterFactory;
use App\ValueObject\CosmosInfo;
use PHPUnit\Framework\TestCase;

/**
 * @covers App\Ds\PresenterFactory
 */
class PresenterFactoryTest extends TestCase
{
    /** @var PresenterFactory */
    private $factory;

    /** @var CosmosInfo */
    private $mockCosmosInfo;

    public function setUp()
    {
        $this->mockCosmosInfo = $this->createMock(CosmosInfo::class);
        $this->factory = new PresenterFactory($this->mockCosmosInfo);
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
