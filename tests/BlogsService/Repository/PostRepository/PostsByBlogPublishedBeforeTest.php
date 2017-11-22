<?php
declare(strict_types = 1);

namespace Tests\App\BlogsService\Repository\PostRepository;

use DateTimeImmutable;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;

class PostsByBlogPublishedBeforeTest extends AbstractPostRepositoryTest
{
    public function testPostsByBlogEmptyOn404()
    {
        $mock404Response = $this->buildMockResponse(404);

        $repo = $this->createPostRepo([
            new ClientException('Error Communicating with Server', new Request('GET', 'test'), $mock404Response),
        ]);

        $result = $repo->getPostsByBlogPublishedBefore('blog-id', new DateTimeImmutable(), 1, 1, 1, 'desc');

        $this->assertNull($result);
    }
}
