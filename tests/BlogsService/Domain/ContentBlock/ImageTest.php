<?php
declare(strict_types = 1);
namespace Tests\App\BlogsService\Domain\ContentBlock;

use App\BlogsService\Domain\ContentBlock\Image;
use App\BlogsService\Domain\Image as DomainImage;
use PHPUnit\Framework\TestCase;

class ImageTest extends TestCase
{
    public function testConstructor()
    {
        $img = $this->createMock(DomainImage::class);

        $image = new Image($img, 'caption');

        $this->assertEquals($img, $image->getImage());
        $this->assertEquals('caption', $image->getCaption());
        $this->assertEquals(200, $image->getCharacterCount());
    }
}
