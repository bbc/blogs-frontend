<?php
declare(strict_types = 1);

namespace Tests\App\BlogsService\Repository\BlogRepository;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;

class GetBlogByIdTest extends AbstractBlogRepositoryTest
{
    public function testGetBlogByIdEmptyOn404()
    {
        $mock404Response = $this->buildMockResponse(404);

        $repo = $this->createBlogRepo([
            new ClientException('Error Communicating with Server', new Request('GET', 'test'), $mock404Response),
        ]);

        $result = $repo->getBlogById('someid');

        $this->assertNull($result);
    }
}
