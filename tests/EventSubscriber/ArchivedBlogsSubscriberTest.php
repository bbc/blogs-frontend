<?php
declare (strict_types = 1);

namespace Tests\App\EventSubscriber;

use App\BlogsService\Infrastructure\IsiteResult;
use App\BlogsService\Service\LegacyBlogService;
use GuzzleHttp\Psr7\Response;
use PHPUnit_Framework_MockObject_MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\App\BlogsService\Service\ServiceTest;
use App\EventSubscriber\ArchivedBlogsSubscriber;

class ArchivedBlogsSubscriberTest extends ServiceTest
{
    private $mockLegacyBlogService;

    /** @var ArchivedBlogsSubscriber */
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
    public function testSomeThingsEndToEnd(string $inputPath, $expectedCall)
    {
        if ($expectedCall) {
            $this->mockLegacyBlogService->expects($this->once())
                ->method('getLegacyBlog')
                ->with($expectedCall);
        } else {
            $this->mockLegacyBlogService->expects($this->never())
                ->method('getLegacyBlog');
        }
        $mockRequest = $this->createConfiguredMock(Request::class, [
            'getPathInfo' => $inputPath,
        ]);
        $mockHttpNotFoundException = $this->createMock(NotFoundHttpException::class);
        $mockExceptionEvent = $this->createConfiguredMock(GetResponseForExceptionEvent::class, [
            'getException' => $mockHttpNotFoundException,
            'getRequest' => $mockRequest,
        ]);
        $this->subscriber->exceptionEvent($mockExceptionEvent);
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
            'directory-traversal-1' => [
                'blogs/../secret/nuclear/codes.txt',
                null,
            ],
            'directory-traversal-2' => [
                'blogs/bbcinternet/2008/.../...//.../...//.../...//.../...//.../...//.../...//.../...//.../...//etc/passwd',
                null,
            ],
            'hmm' => [
                'blogs/bbcinternet/2008/%2e%2e/wibble',
                'blogs/bbcinternet/2008/2e2e/wibble/',
            ],
            'over-long-path' => [
                $this->generateLongString(1000),
                null,
            ],
        ];
    }

    private function generateLongString(int $length)
    {
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= 'Q';
        }
        return $str;
    }
}
