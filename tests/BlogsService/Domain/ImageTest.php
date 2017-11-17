<?php
declare(strict_types=1);

namespace Tests\App\BlogsService\Domain;

use App\BlogsService\Domain\Image;
use PHPUnit\Framework\TestCase;

class ImageTest extends TestCase
{
    public function testConstructorSetsMembers()
    {
        $imageId = "p02kzt0l";
        $testObj = new Image($imageId);

        $this->assertEquals('https://ichef.bbci.co.uk/images/ic/1200xn/' . $imageId . '.jpg', $testObj->getUrl(1200));
    }

    public function testWidthSetting()
    {
        $imageId = "p02kzt0l";
        $testObj = new Image($imageId);

        $this->assertEquals('https://ichef.bbci.co.uk/images/ic/1200xn/' . $imageId . '.jpg', $testObj->getUrl(1200));
    }

    public function testWidthAndHeightSetting()
    {
        $imageId = "p02kzt0l";
        $testObj = new Image($imageId);

        $this->assertEquals('https://ichef.bbci.co.uk/images/ic/1200x800/' . $imageId . '.jpg', $testObj->getUrl(1200, 800));
    }

    public function testJPGExtensionAppend()
    {
        $imageId = "p02kzt0l.jpg";
        $testObj = new Image($imageId);

        $this->assertEquals('https://ichef.bbci.co.uk/images/ic/1200x800/' . $imageId, $testObj->getUrl(1200, 800));
    }
}
