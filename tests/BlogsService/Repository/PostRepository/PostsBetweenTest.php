<?php
declare(strict_types = 1);

namespace Tests\App\BlogsService\Repository\PostRepository;

use Cake\Chronos\Chronos;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;

class PostsBetweenTest extends AbstractPostRepositoryTest
{
    public function testPostsAfterEmptyOn404()
    {
        $mock404Response = $this->buildMockResponse(404);

        $repo = $this->createPostRepo([
            new ClientException('Error Communicating with Server', new Request('GET', 'test'), $mock404Response),
        ]);

        $now = Chronos::now();
        $result = $repo->getPostsBetween('blog-id', $now, $now, 1, 1, 1, 'asc');

        $this->assertNull($result);
    }
}
