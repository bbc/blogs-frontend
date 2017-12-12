<?php
declare(strict_types = 1);

namespace Tests\App\Ds\SidebarModule;

use App\BlogsService\Domain\Module\Links;
use Symfony\Component\DomCrawler\Crawler;
use Tests\App\BaseTemplateTestCase;
use Tests\App\TwigEnvironmentProvider;

class LinksTemplateTest extends BaseTemplateTestCase
{
    public function testMinimalModule()
    {
        $crawler = $this->createCrawler();

        $h2 = $crawler->filterXPath('//h2')->first();
        $this->assertEquals('The Links Module', $h2->text());

        $links = $crawler->filterXPath('//li');
        $this->assertEquals(2, $links->count());

        $firstLink = $links->first();
        $this->assertEquals('Link A (link a)', trim($firstLink->filterXPath('//h3')->first()->text()));
        $this->assertEquals('placea', $firstLink->filterXPath('//a')->first()->attr('href'));
        $this->assertEquals('Link A', $firstLink->filterXPath('//a')->first()->text());
        $this->assertEquals(0, $firstLink->filterXPath('//p')->count());

        $second = $links->eq(1);
        $this->assertEquals('Link B (link b)', trim($second->filterXPath('//h3')->first()->text()));
        $this->assertEquals('placeb', $second->filterXPath('//a')->first()->attr('href'));
        $this->assertEquals('Link B', $second->filterXPath('//a')->first()->text());
        $this->assertEquals('Link B Description', $second->filterXPath('//p')->first()->text());
    }

    private function createCrawler(): Crawler
    {
        $linka = ['link' => 'placea', 'title' => 'Link A', 'caption' => 'link a', 'description' => ''];
        $linkb = ['link' => 'placeb', 'title' => 'Link B', 'caption' => 'link b', 'description' => 'Link B Description'];
        $links = new Links('The Links Module', [$linka, $linkb]);

        $presenterFactory = TwigEnvironmentProvider::dsPresenterFactory();
        $presenter = $presenterFactory->linksModulePresenter($links);

        return $this->presenterCrawler($presenter);
    }
}
