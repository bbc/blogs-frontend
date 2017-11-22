<?php
declare(strict_types = 1);

namespace Tests\App\BlogsService\Repository\PostRepository;

use DateTimeImmutable;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;

class PostsBeforeTest extends AbstractPostRepositoryTest
{
    public function testPostsBeforeEmptyOn404()
    {
        $mock404Response = $this->buildMockResponse(404);

        $repo = $this->createPostRepo([
            new ClientException('Error Communicating with Server', new Request('GET', 'test'), $mock404Response),
        ]);

        $result = $repo->getPostsBefore('blog-id', new DateTimeImmutable(), new DateTimeImmutable(), 1, 1);

        $this->assertNull($result);
    }
}
