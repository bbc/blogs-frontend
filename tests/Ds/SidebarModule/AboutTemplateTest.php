<?php
declare(strict_types = 1);

namespace Tests\App\Ds\SidebarModule;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Domain\Image;
use App\BlogsService\Domain\ValueObject\Social;
use Symfony\Component\DomCrawler\Crawler;
use Tests\App\BaseTemplateTestCase;
use Tests\App\TwigEnvironmentProvider;

class AboutTemplateTest extends BaseTemplateTestCase
{
    public function testMinimalModule()
    {
        $crawler = $this->createCrawler();

        $h2 = $crawler->filterXPath('//h2')->first();
        $this->assertEquals('About this Blog', $h2->text());

        $innerDiv = $crawler->filterXPath('//div//div')->first();
        $contents = $innerDiv->children();
        $this->assertEquals(1, $contents->count()); //Image does not exist
        $prose = $contents->first();
        $this->assertEquals('div', $prose->nodeName());
        $this->assertHasClasses('grid', $prose, 'Prose div classes');

        $description = $crawler->filterXPath('//p')->first();
        $this->assertEquals('This is the description', $description->text());

        $this->assertEquals(0, $crawler->filterXPath('//a[contains(@href, "facebook")]')->count());
        $this->assertEquals(0, $crawler->filterXPath('//a[contains(@href, "twitter")]')->count());

        $ul = $crawler->filterXPath('//ul')->first();
        $this->assertHasClasses('list-unstyled', $ul, 'ul classes');

        $lis = $ul->children();
        $this->assertEquals(2, $lis->count());

        $blogHomeLi = $lis->first();
        $this->assertEquals(1, $blogHomeLi->children()->count());
        $blogHomeLink = $blogHomeLi->children()->first();
        $this->assertEquals('sidebar-home', $blogHomeLink->attr('data-bbc-title'));
        $this->assertEquals('Blog home', $blogHomeLink->text());
        $this->assertEquals('/blogs/theblogid', $blogHomeLink->attr('href'));
    }

    public function testModuleWithImage()
    {
        $crawler = $this->createCrawler(true);

        $innerDiv = $crawler->filterXPath('//div//div')->first();
        $contents = $innerDiv->children();
        $this->assertEquals(2, $contents->count());

        $imageDiv = $contents->first();
        $this->assertEquals('div', $imageDiv->nodeName());
        $this->assertHasClasses('grid 1/2@bpb2 1/2@bpw 1/1@bpw2 1/1@bpe', $imageDiv, 'Image div classes');

        $prose = $contents->eq(1);
        $this->assertEquals('div', $prose->nodeName());
        $this->assertHasClasses('grid 1/2@bpb2 1/2@bpw 1/1@bpw2 1/1@bpe', $prose, 'Prose div classes');
    }

    public function testPartSocial()
    {
        $crawler = $this->createCrawler(false, 'http://www.facebook.com/bbc');

        $this->assertEquals(0, $crawler->filterXPath('//a[contains(@href, "twitter")]')->count());

        $facebook = $crawler->filterXPath('//a[contains(@href, "facebook")]');
        $this->assertEquals(1, $facebook->count());
        $link = $facebook->first();
        $this->assertEquals('a', $link->nodeName());
        $this->assertEquals('_blank', $link->attr('target'));
        $this->assertEquals('http://www.facebook.com/bbc', $link->attr('href'));
    }

    public function testFullSocial()
    {
        $crawler = $this->createCrawler(false, 'http://www.facebook.com/bbc', '@bbc');

        $facebook = $crawler->filterXPath('//a[contains(@href, "facebook")]');
        $this->assertEquals(1, $facebook->count());
        $link = $facebook->first();
        $this->assertEquals('a', $link->nodeName());
        $this->assertEquals('_blank', $link->attr('target'));
        $this->assertEquals('http://www.facebook.com/bbc', $link->attr('href'));

        $twitter = $crawler->filterXPath('//a[contains(@href, "twitter")]');
        $link = $twitter->first();
        $this->assertEquals('_blank', $link->attr('target'));
        $this->assertEquals('http://twitter.com/bbc', $link->attr('href'));
    }

    private function createCrawler(bool $withImage = false, string $facebookUrl = '', string $twitterUsername = ''): Crawler
    {
        $blog = $this->createMock(Blog::class);
        $blog->method('getId')->willReturn('theblogid');
        $blog->method('getDescription')->willReturn('This is the description');
        $blog->method('getShowImageInDescription')->willReturn($withImage);
        $blog->method('getImage')->willReturn($withImage ? new Image('imageid') : null);
        $blog->method('getName')->willReturn('The Blog Name');

        if (!empty($facebookUrl) || !empty($twitterUsername)) {
            $social = $this->createMock(Social::class);
            $social->method('getFacebookUrl')->willReturn($facebookUrl);
            $social->method('getTwitterUsername')->willReturn($twitterUsername);
            $blog->method('getSocial')->willReturn($social);
        }

        $presenterFactory = TwigEnvironmentProvider::dsPresenterFactory();
        $presenter = $presenterFactory->aboutModulePresenter($blog);

        return $this->presenterCrawler($presenter);
    }
}
