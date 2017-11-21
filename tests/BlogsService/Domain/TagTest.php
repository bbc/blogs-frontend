<?php
declare(strict_types=1);

namespace Tests\App\BlogsService\Domain;

use App\BlogsService\Domain\Tag;
use App\BlogsService\Domain\ValueObject\FileID;
use PHPUnit\Framework\TestCase;

class TagTest extends TestCase
{
    public function testConstructorSetsMembers()
    {
        $fileId = new FileID("blogs-author-1424161719");
        $name = "bbc";

        $testObj = new Tag($fileId, $name);

        $this->assertSame($fileId, $testObj->getFileId());
        $this->assertEquals($name, $testObj->getName());
    }
}
