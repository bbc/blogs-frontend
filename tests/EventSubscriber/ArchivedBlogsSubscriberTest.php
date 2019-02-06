<?php
declare (strict_types = 1);

namespace Tests\App\EventSubscriber;

use App\BlogsService\Infrastructure\IsiteResult;
use App\BlogsService\Service\LegacyBlogService;
use GuzzleHttp\Psr7\Response;
use PHPUnit_Framework_MockObject_MockObject;
use Tests\App\BlogsService\Service\ServiceTest;
use App\EventSubscriber\ArchivedBlogsSubscriber;

class ArchivedBlogsSubscriberTest extends ServiceTest
{
    /**
     * @var LegacyBlogService
     */
    private $mockLegacyBlogService;

    /**
     * @var ArchivedBlogsSubscriber
     */
    private $subscriber;

    public function setUp()
    {
        $this->mockLegacyBlogService = $this->createMock(LegacyBlogService::class);

        $this->setUpMockResponseHandler();
        $this->subscriber = new ArchivedBlogsSubscriber($this->mockLegacyBlogService);
    }

    /**
     * @dataProvider archivedBlogsSubscriberRegexDataProvider
     */
    public function testArchivedBlogsSubscriberRegex(string $path, string $expected)
    {
        $reflection = new \ReflectionClass($this->subscriber);
        $method = $reflection->getMethod('cleanUpPath');
        $method->setAccessible(true);

        $this->assertEquals($expected, $method->invokeArgs($this->subscriber, [$path]));
    }

    public function archivedBlogsSubscriberRegexDataProvider()
    {
        return [
            'valid-url' => [
                'blogs/chrismoyles/2006/06/any_questions.shtml',
                'blogs/chrismoyles/2006/06/any_questions.shtml',
            ],
            'preceeding-slash' => [
                '/blogs/chrismoyles/2006/06/any_questions.shtml',
                'blogs/chrismoyles/2006/06/any_questions.shtml',
            ],
            'double-blogs' => [
                '/blogs/blogs/thanks.html',
                'blogs/blogs/thanks.html',
            ],
            'query-strings' => [
                'blogs/chrismoyles/2006/06/any_questions.shtml?foobar',
                'blogs/chrismoyles/2006/06/any_questions.shtml',
            ],
            'trailing-slash' => [
                'blogs/foobar/made/up/stuff/here',
                'blogs/foobar/made/up/stuff/here/',
            ],
            'directory-traversal' => [
                'blogs/../secret/nuclear/codes.txt',
                'blogs/secret/nuclear/codes.txt',

            ],
        ];
    }
}
