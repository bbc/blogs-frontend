<?php
declare(strict_types = 1);
namespace Tests\App\BlogsService\Domain\ValueObject;

use App\BlogsService\Domain\ValueObject\FileID;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class FileIDTest extends TestCase
{
    public function testInvalidArgumentException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The FileID supplied is invalid!');

        new FileID('**INVALIDVALUE%');
    }

    public function testToString()
    {
        $fileId = 'blogs-author-1424161719';
        $testObj = new FileID($fileId);

        $this->assertEquals($fileId, (string) $testObj);
    }
}
