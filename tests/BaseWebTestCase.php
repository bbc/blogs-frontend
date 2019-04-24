<?php
declare(strict_types = 1);
namespace Tests\App;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Domain\Tag;
use App\BlogsService\Infrastructure\IsiteResult;
use App\BlogsService\Service\BlogService;
use App\BlogsService\Service\TagService;
use App\Helper\ApplicationTimeProvider;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

abstract class BaseWebTestCase extends WebTestCase
{
    /** @var Client $client */
    protected $client;

    public function assertResponseStatusCode($client, $expectedCode)
    {
        $actualCode = $client->getResponse()->getStatusCode();
        $this->assertEquals($expectedCode, $actualCode, sprintf(
            'Failed asserting that the response status code "%s" matches expected "%s"',
            $actualCode,
            $expectedCode
        ));
    }

    public function assertRedirectTo($client, $code, $expectedLocation)
    {
        $this->assertResponseStatusCode($client, $code);
        $this->assertEquals($expectedLocation, $client->getResponse()->headers->get('location'));
    }

    public function assertHasRequiredResponseHeaders($client, $cacheControl = 'max-age=300, public', $contentLanguage = null)
    {
        $this->assertEquals($cacheControl, $client->getResponse()->headers->get('Cache-Control'));
        $this->assertArraySubset(['X-CDN', 'X-BBC-Edge-Scheme'], $client->getResponse()->getVary());
        $this->assertEquals('IE=edge', $client->getResponse()->headers->get('X-UA-Compatible'));
        $this->assertEquals('blogs-frontend', $client->getResponse()->headers->get('X-Webapp'));
        $this->assertEquals('stale-while-revalidate=30', $client->getResponse()->headers->get('X-Cache-Control'));
        if (isset($contentLanguage)) {
            $this->assertEquals($contentLanguage, $client->getResponse()->headers->get('Content-Language'));
        } else {
            $this->assertNotEmpty($client->getResponse()->headers->get('Content-Language'));
        }
    }

    public function setUp()
    {
        $this->client = static::createClient();
    }

    public function tearDown()
    {
        ApplicationTimeProvider::clearDateTime();
        parent::tearDown();
    }

    /**
     * @param Crawler $crawler
     * @return string[]
     */
    public function extractIstatsLabels(Crawler $crawler): array
    {
        $labels = [];
        $extractedValues = $crawler->filter('orbit-template-params')->attr('data-values');
        $labelsObject = json_decode($extractedValues);
        foreach ($labelsObject->analyticsLabels as $item) {
            $labels[$item->key] = urldecode($item->value);
        }

        return $labels;
    }

    protected function setTestBlog()
    {
        $blog = $this->createMock(Blog::class);

        $blogService = $this->createMock(BlogService::class);
        $blogService->method('getBlogById')->willReturn($blog);

        $this->client->getContainer()->set(BlogService::class, $blogService);
    }

    protected function setTestTags()
    {
        $tags = [
            $this->createMock(Tag::class),
            $this->createMock(Tag::class),
        ];

        $isiteResultTags = new IsiteResult(1, 1, count($tags), $tags);

        $tagService = $this->createMock(TagService::class);
        $tagService->method('getTagsByBlog')->willReturn($isiteResultTags);

        $this->client->getContainer()->set(TagService::class, $tagService);
    }
}
