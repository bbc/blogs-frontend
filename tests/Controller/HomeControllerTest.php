<?php
declare(strict_types = 1);

namespace Tests\App\Controller;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Infrastructure\IsiteResult;
use App\BlogsService\Service\BlogService;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\DomCrawler\Crawler;
use Tests\App\BaseWebTestCase;
use Tests\App\Builders\BlogBuilder;

/**
 * @covers \App\Controller\HomeController
 */
class HomeControllerTest extends BaseWebTestCase
{
    public function testOneBlog()
    {
        $client = static::createClient();

        $blog = BlogBuilder::default()->withName('First Blog')->build();

        $crawler = $this->getCrawlerForPage($client, [$blog]);

        $labels = $this->extractIstatsLabels($crawler);
        $this->assertEquals('blogs5', $labels['app_name']);
        $this->assertEquals('blogs', $labels['prod_name']);
        $this->assertEquals('index_index', $labels['blogs_page_type']);
        $this->assertEquals('1', $labels['page_number']);
        $this->assertEquals('false', $labels['has_emp']);
        $this->assertEquals('main index for all blogs', $labels['page_type']);
        $this->assertTrue(is_numeric($labels['app_version']));

        $this->assertLists($crawler, ['F' => ['First Blog']]);
        $this->assertResponseStatusCode($client, 200);
        $this->assertHasRequiredResponseHeaders($client);
    }

    public function testTwoBlogsWithSameFirstLetter()
    {
        $client = static::createClient();

        $blog = BlogBuilder::default()->withName('First Blog')->build();
        $blog2 = BlogBuilder::default()->withName('Fake Blog')->build();

        $crawler = $this->getCrawlerForPage($client, [$blog, $blog2]);

        $this->assertLists($crawler, ['F' => ['First Blog', 'Fake Blog']]);
        $this->assertResponseStatusCode($client, 200);
        $this->assertHasRequiredResponseHeaders($client);
    }

    public function testTwoBlogsWithDifferentFirstLetters()
    {
        $client = static::createClient();

        $blog = BlogBuilder::default()->withName('First Blog')->build();
        $blog2 = BlogBuilder::default()->withName('Second Blog')->build();

        $crawler = $this->getCrawlerForPage($client, [$blog, $blog2]);

        $this->assertLists($crawler, ['F' => ['First Blog'], 'S' => ['Second Blog']]);
        $this->assertResponseStatusCode($client, 200);
        $this->assertHasRequiredResponseHeaders($client);
    }

    public function testArchivedBlogDoesntShow()
    {
        $client = static::createClient();

        $blog = BlogBuilder::default()->withName('First Blog')->withIsArchived(true)->build();
        $blog2 = BlogBuilder::default()->withName('Second Blog')->build();

        $crawler = $this->getCrawlerForPage($client, [$blog, $blog2]);

        $this->assertLists($crawler, ['S' => ['Second Blog']]);
        $this->assertResponseStatusCode($client, 200);
        $this->assertHasRequiredResponseHeaders($client);
    }

    public function testBlogsHaveUndesireablePrefixesRemovedWhenChoosingGrouping()
    {
        $client = static::createClient();

        $blog = BlogBuilder::default()->withName('BBC Blog A Name')->build();
        $blog2 = BlogBuilder::default()->withName('Blog BBC Some Name')->build();

        $crawler = $this->getCrawlerForPage($client, [$blog, $blog2]);

        $this->assertLists($crawler, ['A' => ['BBC Blog A Name'], 'B' => ['Blog BBC Some Name']]);
        $this->assertResponseStatusCode($client, 200);
        $this->assertHasRequiredResponseHeaders($client);
    }

    public function testNoBlogs()
    {
        $client = static::createClient();

        $this->getCrawlerForPage($client, []);

        $this->assertResponseStatusCode($client, 200);
        $this->assertHasRequiredResponseHeaders($client);
    }

    public function testNoNonArchivedBlogs()
    {
        $client = static::createClient();

        $blog = BlogBuilder::default()->withIsArchived(true)->build();

        $crawler = $this->getCrawlerForPage($client, [$blog]);

        $this->assertCount(1, $crawler->filter('h1+div')->first()->filter('p'));
        $this->assertEquals('There are no results', $crawler->filter('h1+div')->first()->filter('p')->text());
        $this->assertResponseStatusCode($client, 200);
        $this->assertHasRequiredResponseHeaders($client);
    }

    /**
     * @param Crawler $h2
     * @param Crawler $h3s
     * @param string $heading
     * @param string[] $titles
     */
    private function assertList(Crawler $h2, Crawler $h3s, string $heading, array $titles)
    {
        $this->assertEquals($heading, $h2->text());
        $this->assertCount(count($titles), $h3s);
        foreach ($titles as $key => $title) {
            $this->assertEquals($title, $h3s->eq($key)->text());
        }
    }

    /**
     * @param Crawler $crawler
     * @param string[][] $expectedList
     */
    private function assertLists(Crawler $crawler, array $expectedList)
    {
        $expectedListCount = count($expectedList);
        $h2s = $crawler->filter('h2');
        $lists = $crawler->filter('h2+ul');
        $this->assertCount($expectedListCount, $h2s);
        $this->assertCount($expectedListCount, $lists);
        $index = 0;
        foreach ($expectedList as $heading => $titles) {
            $this->assertList($h2s->eq($index), $lists->eq($index)->filter('h3'), $heading, $titles);
            ++$index;
        }
    }

    /**
     * @param Client $client
     * @param Blog[] $blogs
     * @return Crawler
     */
    private function getCrawlerForPage(Client $client, array $blogs): Crawler
    {
        $isiteResult = new IsiteResult(1, 30, count($blogs), $blogs);

        $service = $this->createMock(BlogService::class);
        $service->method('getAllBlogs')->willReturn($isiteResult);

        $client->getContainer()->set(BlogService::class, $service);

        return $client->request('GET', '/blogs');
    }
}
