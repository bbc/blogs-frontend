<?php
declare(strict_types=1);

namespace Tests\App\BlogsService\Domain\Module;

use App\BlogsService\Domain\Module\Links;
use PHPUnit\Framework\TestCase;

class LinksModuleTest extends TestCase
{
    public function testConstructorSetsMembers()
    {
        $moduleTitle = 'side text Jan 2015';
        $links = [];

        $testObj = new Links($moduleTitle, $links);

        $this->assertEquals($moduleTitle, $testObj->getTitle());
        $this->assertInternalType('array', $testObj->getLinks());
        $this->assertEquals($links, $testObj->getLinks());
    }
}
