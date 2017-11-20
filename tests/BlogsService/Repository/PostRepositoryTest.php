<?php
declare(strict_types = 1);

namespace Tests\App\BlogsService\Repository;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Repository\PostRepository;
use DateTimeImmutable;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;

class PostRepositoryTest extends RepositoryTest
{
    public function testPostsByBlogEmptyOn404()
    {
        $mock404Response = $this->buildMockResponse(404);

        $repo = $this->createPostRepo([
            new ClientException('Error Communicating with Server', new Request('GET', 'test'), $mock404Response),
        ]);

        $mockBlog = $this->createMock(Blog::class);

        $result = $repo->getPostsByBlog($mockBlog, new DateTimeImmutable(), 1, 1, 'desc');

        $this->assertNull($result);
    }

    /**
     * @param GuzzleException[]|ResponseInterface[] $responses
     * @return PostRepository
     */
    private function createPostRepo(array $responses): PostRepository
    {
        $mock = new MockHandler($responses);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        return new PostRepository(self::API_ENDPOINT, $client);
    }
}
