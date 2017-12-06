<?php
declare(strict_types = 1);

namespace Tests\App\ValueObject;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Domain\ValueObject\Comments;
use App\ValueObject\IstatsAnalyticsLabels;
use PHPUnit\Framework\TestCase;

class IstatsAnalyticsLabelsTest extends TestCase
{
    public function testMinimumLabels()
    {
        $istats = new IstatsAnalyticsLabels(null, 'page_type', 'v1.2.3-4', false, []);
        $labels = $this->makeLabelsNice($istats->orbLabels());
        $this->assertArrayHasKey('app_name', $labels);
        $this->assertArrayHasKey('app_version', $labels);
        $this->assertArrayHasKey('blogs_page_type', $labels);
        $this->assertArrayHasKey('has_emp', $labels);
        $this->assertArrayHasKey('has_comments', $labels);
        $this->assertArrayHasKey('prod_name', $labels);
        $this->assertArrayNotHasKey('bbc_site', $labels);
        $this->assertArrayNotHasKey('blog_language', $labels);
        $this->assertArrayNotHasKey('blog_project_id', $labels);
        $this->assertArrayNotHasKey('blog_title', $labels);
        $this->assertArrayNotHasKey('page_type', $labels);

        $this->assertEquals($labels['app_name'], 'blogs5');
        $this->assertEquals($labels['app_version'], 'v1.2.3-4');
        $this->assertEquals($labels['blogs_page_type'], 'page_type');
        $this->assertEquals($labels['has_emp'], 'false');
        $this->assertEquals($labels['has_comments'], 'false');
        $this->assertEquals($labels['prod_name'], 'blogs');
    }

    public function testLabelsWithBlog()
    {
        $blog = $this->createMock(Blog::class);
        $blog->method('getBbcSite')->willReturn('bbc_site');
        $blog->method('getComments')->willReturn(new Comments('site_id'));
        $blog->method('getName')->willReturn('name');
        $blog->method('getId')->willReturn('project_id');
        $blog->method('getLanguage')->willReturn('language');
        $istats = new IstatsAnalyticsLabels($blog, 'page_type', 'v1.2.3-4', false, []);
        $labels = $this->makeLabelsNice($istats->orbLabels());
        $this->assertArrayHasKey('bbc_site', $labels);
        $this->assertArrayHasKey('blog_language', $labels);
        $this->assertArrayHasKey('blog_project_id', $labels);
        $this->assertArrayHasKey('blog_title', $labels);

        $this->assertEquals($labels['bbc_site'], 'bbc_site');
        $this->assertEquals($labels['blog_language'], 'language');
        $this->assertEquals($labels['blog_project_id'], 'project_id');
        $this->assertEquals($labels['blog_title'], 'name');
        $this->assertEquals($labels['has_comments'], 'true');
    }

    public function testHasVideo()
    {
        $istats = new IstatsAnalyticsLabels(null, 'page_type', 'v1.2.3-4', true, []);
        $labels = $this->makeLabelsNice($istats->orbLabels());
        $this->assertArrayHasKey('has_emp', $labels);
        $this->assertEquals($labels['has_emp'], 'true');
    }

    public function testExtraLabels()
    {
        $istats = new IstatsAnalyticsLabels(null, 'page_type', 'v1.2.3-4', false, ['geoff' => 'jeff']);
        $labels = $this->makeLabelsNice($istats->orbLabels());
        $this->assertArrayHasKey('geoff', $labels);
        $this->assertEquals($labels['geoff'], 'jeff');
    }

    private function makeLabelsNice(array $orbLabels): array
    {
        $labelsArray = [];
        foreach ($orbLabels as $label) {
            $labelsArray[$label['key']] = urldecode($label['value']);
        }

        return $labelsArray;
    }
}
