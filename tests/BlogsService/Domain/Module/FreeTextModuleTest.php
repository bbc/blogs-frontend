<?php
declare(strict_types=1);

namespace Tests\App\BlogsService\Domain\Module;

use App\BlogsService\Domain\Image;
use App\BlogsService\Domain\Module\FreeText;
use PHPUnit\Framework\TestCase;

class FreeTextModuleTest extends TestCase
{
    public function testConstructorSetsMembersWithoutImage()
    {
        $moduletitle = 'side text Jan 2015';
        $body = '<div>xsadasddsad</div> <div><span>This was the first
            time we split the year into genre based </span>
            <a href="http://www.bbc.co.uk/writersroom/send-a-script/">Script
            Room</a><span> windows, which in the main worked. But when we
            looked at the way we broke it down we questioned whether it was
            the best approach. So we have decided to fine tune the system
            and simplify further. In the spring we will have a </span><strong>
            Comedy</strong><span> window, and in the autumn a </span><strong>
            Drama</strong><span> one. This means that whatever platform your
            Comedy script has been written for - TV, Radio, CBBC or Film, you
            </span></div>';

        $testObj = new FreeText($moduletitle, $body);

        $this->assertEquals($moduletitle, $testObj->getTitle());
        $this->assertEquals($body, $testObj->getBody());
        $this->assertSame(null, $testObj->getImage());
    }

    public function testConstructorSetsMembersWithImage()
    {
        $moduletitle = 'side text Jan 2015';
        $body = '<div>xsadasddsad</div> <div><span>This was the first
            time we split the year into genre based </span>
            <a href="http://www.bbc.co.uk/writersroom/send-a-script/">Script
            Room</a><span> windows, which in the main worked. But when we
            looked at the way we broke it down we questioned whether it was
            the best approach. So we have decided to fine tune the system
            and simplify further. In the spring we will have a </span><strong>
            Comedy</strong><span> window, and in the autumn a </span><strong>
            Drama</strong><span> one. This means that whatever platform your
            Comedy script has been written for - TV, Radio, CBBC or Film, you
            </span></div>';
        $image = new Image('p02kzt0l');

        $testObj = new FreeText($moduletitle, $body, $image);

        $this->assertEquals($moduletitle, $testObj->getTitle());
        $this->assertEquals($body, $testObj->getBody());
        $this->assertSame($image, $testObj->getImage());
    }
}
