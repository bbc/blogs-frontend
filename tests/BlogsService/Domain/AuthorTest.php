<?php
declare(strict_types=1);

namespace Tests\App\BlogsService\Domain;

use App\BlogsService\Domain\Author;
use App\BlogsService\Domain\Image;
use App\BlogsService\Domain\ValueObject\FileID;
use App\BlogsService\Domain\ValueObject\GUID;
use App\BlogsService\Domain\ValueObject\Social;
use PHPUnit\Framework\TestCase;

class AuthorTest extends TestCase
{
    public function testConstructorSetsMembers()
    {
        $guid = new GUID("63a91e43-f154-4c89-9ccd-9cf10a332f90");
        $fileId = new FileID("blogs-author-1424161719");

        $name = "Qambar Raza";

        $role = "Web Developer";
        $description = "BBC Blogs";
        $image = new Image("p02kzt0l");
        $social = new Social(
            "@QambarRaza",
            "https://www.facebook.com/qambarr",
            "https://plus.google.com/114400367936494835546/posts"
        );

        $testObj = new Author(
            $guid,
            $fileId,
            $name,
            $role,
            $description,
            $image,
            $social
        );

        $this->assertSame($guid, $testObj->getGuid());
        $this->assertSame($fileId, $testObj->getFileId());
        $this->assertEquals($name, $testObj->getName());
        $this->assertEquals($role, $testObj->getRole());
        $this->assertEquals($description, $testObj->getDescription());
        $this->assertSame($image, $testObj->getImage());
        $this->assertSame($social, $testObj->getSocial());
    }
}
