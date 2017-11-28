<?php
declare(strict_types = 1);
namespace Tests\App\BlogsService\Domain\ContentBlock;

use App\BlogsService\Domain\ContentBlock\Code;
use PHPUnit\Framework\TestCase;

class CodeTest extends TestCase
{
    public function testConstructor()
    {
        $code = new Code(
            'i-am-some-code'
        );

        $this->assertEquals('i-am-some-code', $code->getCode());
        $this->assertEquals(14, $code->getCharacterCount());
    }
}
