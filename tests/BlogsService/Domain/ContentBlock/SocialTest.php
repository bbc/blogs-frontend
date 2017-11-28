<?php
declare(strict_types = 1);
namespace Tests\App\BlogsService\Domain\ContentBlock;

use App\BlogsService\Domain\ContentBlock\Social;
use PHPUnit\Framework\TestCase;

class SocialTest extends TestCase
{
    public function testConstructor()
    {
        $social = new Social('link', 'alt');

        $this->assertEquals('link', $social->getLink());
        $this->assertEquals('alt', $social->getAlt());
        $this->assertEquals(200, $social->getCharacterCount());
    }
}
