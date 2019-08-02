<?php
declare(strict_types = 1);

namespace Tests\App\Ds\SidebarModule;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Domain\Tag;
use App\BlogsService\Domain\ValueObject\FileID;
use Symfony\Component\DomCrawler\Crawler;
use Tests\App\BaseTemplateTestCase;
use Tests\App\TwigEnvironmentProvider;

class BlogTagsTemplateTest extends BaseTemplateTestCase
{
    public function testMinimalModule()
    {
        $crawler = $this->createCrawler();

        $h2 = $crawler->filterXPath('//h2')->first();
        $this->assertEquals('Tags', $h2->text());

        $tags = $crawler->filterXPath('//li');
        $this->assertEquals(2, $tags->count());

        $firstTag = $tags->first();
        $this->assertEquals('sidebar-tag', $firstTag->filterXPath('//a')->first()->attr('data-bbc-title'));
        $this->assertEquals('/blogs/theblogid/tags/fileid1', $firstTag->filterXPath('//a')->first()->attr('href'));
        $this->assertEquals(' name1', $firstTag->filterXPath('//a')->first()->text());

        $secondTag = $tags->eq(1);
        $this->assertEquals('sidebar-tag', $firstTag->filterXPath('//a')->first()->attr('data-bbc-title'));
        $this->assertEquals('/blogs/theblogid/tags/fileid2', $secondTag->filterXPath('//a')->first()->attr('href'));
        $this->assertEquals(' name2', $secondTag->filterXPath('//a')->first()->text());

        $allTagsLink = $crawler->filterXPath('//p/a')->first();
        $this->assertEquals('sidebar-all-tags', $allTagsLink->attr('data-bbc-title'));
        $this->assertEquals('/blogs/theblogid/tags', $allTagsLink->attr('href'));
        $this->assertEquals('See all tags', $allTagsLink->text());
    }

    private function createCrawler(): Crawler
    {
        $tags = [];
        $tags[] = new Tag(new FileID('tag-fileid1'), 'name1');
        $tags[] = new Tag(new FileID('tag-fileid2'), 'name2');

        $blog = $this->createMock(Blog::class);
        $blog->method('getId')->willReturn('theblogid');

        $presenterFactory = TwigEnvironmentProvider::dsPresenterFactory();
        $presenter = $presenterFactory->blogTagsModulePresenter($blog, $tags);

        return $this->presenterCrawler($presenter);
    }
}
