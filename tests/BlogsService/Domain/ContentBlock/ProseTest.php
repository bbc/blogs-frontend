<?php
declare(strict_types = 1);
namespace Tests\App\BlogsService\Domain\ContentBlock;

use App\BlogsService\Domain\ContentBlock\Prose;
use PHPUnit\Framework\TestCase;

class ProseTest extends TestCase
{
    public function testConstructor()
    {
        $prose = new Prose('<h1>i-am-some-prose</h1>');

        $this->assertEquals('<h1>i-am-some-prose</h1>', $prose->getProse());
        $this->assertEquals(15, $prose->getCharacterCount());
    }
}
