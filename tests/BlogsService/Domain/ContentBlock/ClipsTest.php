<?php
declare(strict_types = 1);
namespace Tests\App\BlogsService\Domain\ContentBlock;

use App\BlogsService\Domain\ContentBlock\Clips;
use PHPUnit\Framework\TestCase;

class ClipsTest extends TestCase
{
    public function testConstructor()
    {
        $clips = new Clips(
            'id',
            'url',
            'caption',
            'playlistType'
        );

        $this->assertEquals('id', $clips->getId());
        $this->assertEquals('url', $clips->getUrl());
        $this->assertEquals('caption', $clips->getCaption());
        $this->assertEquals('playlistType', $clips->getPlaylistType());
        $this->assertEquals(200, $clips->getCharacterCount());
    }
}
