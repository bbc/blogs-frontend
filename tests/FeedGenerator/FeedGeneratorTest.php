<?php
declare(strict_types = 1);

namespace Tests\App\FeedGenerator;

use App\BlogsService\Domain\Blog;
use App\Ds\PresenterFactory;
use App\FeedGenerator\FeedGenerator;
use App\Helper\ApplicationTimeProvider;
use App\Translate\TranslateProvider;
use Cake\Chronos\Chronos;
use PHPUnit\Framework\TestCase;
use RMP\Translate\Translate;
use SimpleXMLElement;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Tests\App\Builders\BlogBuilder;
use Tests\App\Builders\PostBuilder;
use Twig_Environment;

class FeedGeneratorTest extends TestCase
{
    public function setUp()
    {
        ApplicationTimeProvider::setDateTime(Chronos::create(2017, 12, 15, 0, 0, 0));
    }

    public function tearDown()
    {
        ApplicationTimeProvider::clearDateTime();
    }

    public function testRssNoPosts()
    {
        $blog = BlogBuilder::default()->withName('Blog name')->withDescription('Blog description')->build();

        $xml = $this->getRssFeed($blog, []);

        $this->assertRssRequiredFeedFields($xml, 'Blog name translation', 'Blog description', 'http://somevalidurl.com');
    }

    public function testRssWithPosts()
    {
        $blog = BlogBuilder::default()->withName('Blog name')->withDescription('Blog description')->build();
        $posts = [
            PostBuilder::default()->withTitle('Post title')->withShortSynopsis('short syn')->build(),
        ];

        $xml = $this->getRssFeed($blog, $posts);

        $this->assertRssRequiredFeedFields($xml, 'Blog name translation', 'Blog description', 'http://somevalidurl.com');
        $this->assertRssRequiredItemFields($xml, 'Post title', 'short syn');
    }

    public function testAtomNoPosts()
    {
        $blog = BlogBuilder::default()->withName('Blog name')->build();

        $xml = $this->getAtomFeed($blog, []);

        $this->assertAtomRequiredFeedFields($xml, 'Blog name translation', 'http://somevalidurl.com', '2017-12-15T00:00:00+00:00');
    }

    public function testAtomWithPosts()
    {
        $blog = BlogBuilder::default()->withName('Blog name')->build();
        $posts = [
            PostBuilder::default()
                ->withTitle('Post title')
                ->withPublishedDate(Chronos::create(2002, 5, 2, 23, 30, 0))
                ->build(),
        ];

        $xml = $this->getAtomFeed($blog, $posts);

        $this->assertAtomRequiredFeedFields($xml, 'Blog name translation', 'http://somevalidurl.com', '2002-05-02T23:30:00+00:00');
        $this->assertAtomRequiredItemFields($xml, 'Post title', 'http://somevalidurl.com', '2002-05-02T23:30:00+00:00');
    }

    public function testAtomDisplaysHtmlContent()
    {
        $blog = BlogBuilder::default()->build();
        $posts = [PostBuilder::default()->build()];

        $xml = $this->getAtomFeed($blog, $posts);

        $this->assertEquals('html', $xml->entry->content->attributes()['type']);
        $this->assertEquals('<div class="someclass"><p>Here is some content</p></div>', trim((string) $xml->entry->content));
    }

    private function assertRssRequiredFeedFields(SimpleXMLElement $feed, string $expectedTitle, string $expectedDesc, string $expectedLink)
    {
        $this->assertEquals($expectedTitle, (string) $feed->channel->title);
        $this->assertEquals($expectedDesc, (string) $feed->channel->description);
        $this->assertEquals($expectedLink, (string) $feed->channel->link);
    }

    private function assertRssRequiredItemFields(SimpleXMLElement $feed, string $expectedTitle, string $expectedDesc)
    {
        $this->assertEquals($expectedTitle, (string) $feed->channel->item->title);
        $this->assertEquals($expectedDesc, (string) $feed->channel->item->description);
    }

    private function assertAtomRequiredFeedFields(SimpleXMLElement $feed, string $expectedTitle, string $expectedId, string $expectedUpdated)
    {
        $this->assertEquals($expectedTitle, (string) $feed->title);
        $this->assertEquals($expectedId, (string) $feed->id);
        $this->assertEquals($expectedUpdated, (string) $feed->updated);
    }

    private function assertAtomRequiredItemFields(SimpleXMLElement $feed, string $expectedTitle, string $expectedId, string $expectedUpdated)
    {
        $this->assertEquals($expectedTitle, (string) $feed->entry->title);
        $this->assertEquals($expectedId, (string) $feed->entry->id);
        $this->assertEquals($expectedUpdated, (string) $feed->entry->updated);
    }

    private function getAtomFeed(Blog $blog, array $posts): SimpleXMLElement
    {
        $feedGenerator = $this->createFeedGenerator();
        $feedData = $feedGenerator->generateAtomFeed($blog, $posts);
        /** @var SimpleXMLElement $result */
        $result = simplexml_load_string($feedData);

        return $result;
    }

    private function getRssFeed(Blog $blog, array $posts): SimpleXMLElement
    {
        $feedGenerator = $this->createFeedGenerator();
        $feedData = $feedGenerator->generateRssFeed($blog, $posts);
        /** @var SimpleXMLElement $result */
        $result = simplexml_load_string($feedData);

        return $result;
    }

    private function createFeedGenerator(): FeedGenerator
    {
        $translate = $this->createMock(Translate::class);
        $translate->method('translate')->willReturn('translation');
        $translateProvider = $this->createMock(TranslateProvider::class);
        $translateProvider->method('getTranslate')->willReturn($translate);

        $router = $this->createMock(UrlGeneratorInterface::class);
        $router->method('generate')->willReturn('http://somevalidurl.com');

        $twigEnvironment = $this->createMock(Twig_Environment::class);
        $twigEnvironment->method('render')->willReturn('<div class="someclass"><p>Here is some content</p></div>');

        $presenterFactory = $this->createMock(PresenterFactory::class);

        return new FeedGenerator($translateProvider, $router, $twigEnvironment, $presenterFactory);
    }
}
