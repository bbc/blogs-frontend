<?php
declare(strict_types = 1);

namespace Tests\App\Controller;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Domain\Image;
use App\BlogsService\Domain\ValueObject\Social;
use App\BlogsService\Infrastructure\IsiteResult;
use App\BlogsService\Service\BlogService;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\DomCrawler\Crawler;
use Tests\App\BaseWebTestCase;

/**
 * @covers \App\Controller\HomeController
 */
class HomeControllerTest extends BaseWebTestCase
{
    public function testOneBlog()
    {
        $client = static::createClient();

        $blog = $this->createBlog('First Blog');

        $crawler = $this->getCrawlerForPage($client, [$blog]);

        $this->assertLists($crawler, ['F' => ['First Blog']]);
        $this->assertResponseStatusCode($client, 200);
        $this->assertHasRequiredResponseHeaders($client);
    }

    public function testTwoBlogsWithSameFirstLetter()
    {
        $client = static::createClient();

        $blog = $this->createBlog('First Blog');
        $blog2 = $this->createBlog('Fake Blog');

        $crawler = $this->getCrawlerForPage($client, [$blog, $blog2]);

        $this->assertLists($crawler, ['F' => ['First Blog', 'Fake Blog']]);
        $this->assertResponseStatusCode($client, 200);
        $this->assertHasRequiredResponseHeaders($client);
    }

    public function testTwoBlogsWithDifferentFirstLetters()
    {
        $client = static::createClient();

        $blog = $this->createBlog('First Blog');
        $blog2 = $this->createBlog('Second Blog');

        $crawler = $this->getCrawlerForPage($client, [$blog, $blog2]);

        $this->assertLists($crawler, ['F' => ['First Blog'], 'S' => ['Second Blog']]);
        $this->assertResponseStatusCode($client, 200);
        $this->assertHasRequiredResponseHeaders($client);
    }

    public function testArchivedBlogDoesntShow()
    {
        $client = static::createClient();

        $blog = $this->createBlog('First Blog', true);
        $blog2 = $this->createBlog('Second Blog');

        $crawler = $this->getCrawlerForPage($client, [$blog, $blog2]);

        $this->assertLists($crawler, ['S' => ['Second Blog']]);
        $this->assertResponseStatusCode($client, 200);
        $this->assertHasRequiredResponseHeaders($client);
    }

    public function testBlogsHaveUndesireablePrefixesRemovedWhenChoosingGrouping()
    {
        $client = static::createClient();

        $blog = $this->createBlog('BBC Blog A Name');
        $blog2 = $this->createBlog('Blog BBC Some Name');

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

        $blog = $this->createBlog('First Blog', true);

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

    private function createBlog(string $name, bool $isArchived = false): Blog
    {
        return new Blog(
            'anything',
            $name,
            'anything',
            'anything',
            false,
            'anything',
            'anything',
            'anything',
            'anything',
            [],
            new Social('', '', ''),
            null,
            null,
            new Image('p0215q0b'), //default provided by mapper
            $isArchived
        );
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
