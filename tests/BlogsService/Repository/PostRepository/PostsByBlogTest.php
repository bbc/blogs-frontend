<?php
declare(strict_types = 1);

namespace Tests\App\BlogsService\Repository\PostRepository;

use App\BlogsService\Domain\Blog;
use DateTimeImmutable;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;

class PostsByBlogTest extends AbstractPostRepositoryTest
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
}
