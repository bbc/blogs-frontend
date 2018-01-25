<?php
declare(strict_types = 1);

namespace Tests\App\Ds\SidebarModule;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Domain\Tag;
use App\BlogsService\Domain\ValueObject\FileID;
use Symfony\Component\DomCrawler\Crawler;
use Tests\App\BaseTemplateTestCase;
use Tests\App\TwigEnvironmentProvider;

class UpdatesTemplateTest extends BaseTemplateTestCase
{
    /**
     * @dataProvider updatesModuleProvider
     */
    public function testModuleShowsCorrectLinks(bool $tag, int $numUls, string $link)
    {
        $crawler = $this->createCrawler($tag);

        $uls = $crawler->filterXPath('//ul');
        $this->assertEquals($numUls, $uls->count());

        $firstRssLink = $uls->first()->filterXPath('//li//a');
        $this->assertEquals($link, $firstRssLink->attr('href'));
    }

    public function updatesModuleProvider(): array
    {
        return [
            'withoutTag' => ['tag' => false, 'numUls' => 1, 'link' => '/blogs/theblogid/rss'],
            'withTag' => ['tag' => true, 'numUls' => 2, 'link' => '/blogs/theblogid/tags/sometagid/rss'],
        ];
    }

    private function createCrawler(bool $withTag): Crawler
    {
        $blog = $this->createMock(Blog::class);
        $blog->method('getId')->willReturn('theblogid');

        $tag = null;

        if ($withTag) {
            $tag = new Tag(new FileID('tag-sometagid'), 'Some Tag');
        }

        $presenterFactory = TwigEnvironmentProvider::dsPresenterFactory();
        $presenter = $presenterFactory->updatesModulePresenter($blog, $tag);

        return $this->presenterCrawler($presenter);
    }
}
