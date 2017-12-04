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
        $outerDiv = $this->createCrawler();

        $this->assertHasClasses('sidebox component br-box-subtle', $outerDiv, 'Outer div classes');
        $outerDivChildren = $outerDiv->children();
        $this->assertEquals(2, $outerDivChildren->count());
        $h2 = $outerDivChildren->first();
        $this->assertEquals('h2', $h2->nodeName());
        $this->assertHasClasses('island--squashed br-box-highlight no-margin', $h2, 'H2 classes');
        $this->assertEquals('About this Blog', $h2->text());
        $innerDiv = $outerDivChildren->eq(1);
        $this->assertEquals('div', $innerDiv->nodeName());
        $this->assertHasClasses('grid-wrapper', $innerDiv, 'Inner div classes');

        $contents = $innerDiv->children();
        $this->assertEquals(1, $contents->count());
        $prose = $contents->first();
        $this->assertEquals('div', $prose->nodeName());
        $this->assertHasClasses('grid', $prose, 'Prose div classes');

        $island = $prose->children()->first();
        $this->assertEquals('div', $island->nodeName());
        $this->assertHasClasses('island', $island, 'Island div classes');
        $islandContents = $island->children();
        $this->assertEquals(2, $islandContents->count());
        $description = $islandContents->first();
        $this->assertEquals('p', $description->nodeName());
        $this->assertEquals('This is the description', $description->text());

        $ul = $islandContents->eq(1);
        $this->assertEquals('ul', $ul->nodeName());
        $this->assertHasClasses('list-unstyled', $ul, 'ul classes');

        $lis = $ul->children();
        $this->assertEquals(2, $lis->count());

        $blogHomeLi = $lis->first();
        $this->assertEquals(1, $blogHomeLi->children()->count());
        $blogHomeLink = $blogHomeLi->children()->first();
        $this->assertHasClasses('istats--tracker', $blogHomeLink, 'Blog Home Link classes');
        $this->assertEquals('blogs_global_aside_home', $blogHomeLink->attr('data-istats-link-location'));
        $this->assertEquals('Blog home', $blogHomeLink->text());
        $this->assertEquals('/blogs/theblogid', $blogHomeLink->attr('href'));
    }

    public function testModuleWithImage()
    {
        $outerDiv = $this->createCrawler(true);

        $outerDivChildren = $outerDiv->children();
        $innerDiv = $outerDivChildren->eq(1);

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
        $outerDiv = $this->createCrawler(false, 'http://www.facebook.com/bbc');

        $outerDivChildren = $outerDiv->children();
        $innerDiv = $outerDivChildren->eq(1);
        $contents = $innerDiv->children();
        $prose = $contents->first();

        $island = $prose->children()->first();
        $islandContents = $island->children();
        $this->assertEquals(3, $islandContents->count());
        $facebook = $islandContents->eq(1);
        $this->assertEquals('p', $facebook->nodeName());
        $this->assertEquals('Find us on Facebook', trim($facebook->text()));
        $this->assertEquals(1, $facebook->children()->count());

        $link = $facebook->children()->first();
        $this->assertEquals('a', $link->nodeName());
        $this->assertEquals('_blank', $link->attr('target'));
        $this->assertEquals('http://www.facebook.com/bbc', $link->attr('href'));
    }

    public function testFullSocial()
    {
        $outerDiv = $this->createCrawler(false, 'http://www.facebook.com/bbc', '@bbc');

        $outerDivChildren = $outerDiv->children();
        $innerDiv = $outerDivChildren->eq(1);
        $contents = $innerDiv->children();
        $prose = $contents->first();

        $island = $prose->children()->first();
        $islandContents = $island->children();
        $this->assertEquals(4, $islandContents->count());

        $twitter = $islandContents->eq(1);
        $this->assertEquals('p', $twitter->nodeName());
        $this->assertEquals('Follow The Blog Name on Twitter', trim($twitter->text()));
        $this->assertEquals(1, $twitter->children()->count());

        $link = $twitter->children()->first();
        $this->assertEquals('a', $link->nodeName());
        $this->assertEquals('_blank', $link->attr('target'));
        $this->assertEquals('http://twitter.com/bbc', $link->attr('href'));

        $facebook = $islandContents->eq(2);
        $this->assertEquals('p', $facebook->nodeName());
        $this->assertEquals('Find us on Facebook', trim($facebook->text()));
        $this->assertEquals(1, $facebook->children()->count());

        $link = $facebook->children()->first();
        $this->assertEquals('a', $link->nodeName());
        $this->assertEquals('_blank', $link->attr('target'));
        $this->assertEquals('http://www.facebook.com/bbc', $link->attr('href'));
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
        $crawler = $this->presenterCrawler($presenter);

        return $crawler->filterXPath('//div')->first();
    }
}
