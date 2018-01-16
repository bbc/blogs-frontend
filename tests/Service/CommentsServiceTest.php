<?php
declare(strict_types=1);

namespace Tests\App\Service;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Domain\Post;
use App\Service\CommentsService;
use App\Translate\TranslateProvider;
use BBC\ProgrammesMorphLibrary\Exception\MorphErrorException;
use BBC\ProgrammesMorphLibrary\MorphClient;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use RMP\Translate\TranslateFactory;

class CommentsServiceTest extends TestCase
{
    private $blog;
    private $post;
    private $logger;
    private $translateProvider;

    public function setup()
    {
        $this->blog = $this->createMock(Blog::class);
        $this->post = $this->createMock(Post::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->translateProvider = new TranslateProvider(new TranslateFactory());
    }

    public function testGetByBlogAndPostHandlesNullResponse()
    {
        $client = $this->createMock(MorphClient::class);
        $client->method('makeCachedViewRequest')->willReturn(null);

        $service = new CommentsService($this->logger, $this->translateProvider, $client, 'asd', 'test');
        $response = $service->getByBlogAndPost($this->blog, $this->post);
        $this->assertEquals([], $response->getHead());
        $this->assertEquals('error_comments', $response->getBody());
        $this->assertEquals([], $response->getBodyLast());
    }
}
