<?php

namespace Tests\App\ValueObject;

use App\BlogsService\Domain\Blog;
use App\ValueObject\AnalyticsCounterName;
use PHPUnit\Framework\TestCase;

class AnalyticsCounterNameTest extends TestCase
{
    public function testMinimalName()
    {
        $counterName = new AnalyticsCounterName(null);
        $this->assertEquals((string) $counterName, 'blogs.page');
    }

    public function testNameWithBlog()
    {
        $blog = $this->createMock(Blog::class);
        $blog->method('getIstatsCountername')->willReturn('IstatsCountername');
        $counterName = new AnalyticsCounterName($blog);
        $this->assertEquals((string) $counterName, 'IstatsCountername.blogs.page');
    }

    public function testNameWithPageName()
    {
        $counterName = new AnalyticsCounterName(null, 'page name');
        $this->assertEquals((string) $counterName, 'blogs.page_name.page');
    }
}
