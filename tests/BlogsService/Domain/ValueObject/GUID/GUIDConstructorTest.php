<?php
declare(strict_types = 1);
namespace Tests\App\BlogsService\Domain\ValueObject\GUID;

use App\BlogsService\Domain\ValueObject\GUID;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

class GUIDConstructorTest extends TestCase
{
    public function testInvalidArgumentException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The GUID supplied (**INVALIDVALUE%) is invalid');

        $testObj = new GUID('**INVALIDVALUE%');
    }

    public function testConstructorSetsMembers()
    {
        $guid = "63a91e43-f154-4c89-9ccd-9cf10a332f90";
        $testObj = new GUID($guid);

        $this->assertEquals($guid, $testObj->getGuid());
    }

    public function testToString()
    {
        $guid = "63a91e43-f154-4c89-9ccd-9cf10a332f90";
        $testObj = new GUID($guid);

        $this->assertEquals($guid, (string) $testObj);
    }
}