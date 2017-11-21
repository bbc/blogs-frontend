<?php
declare(strict_types = 1);

namespace Tests\App\BlogsService\Repository\TagRepository;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;

class TagsByBlogTest extends AbstractTagRepositoryTest
{
    public function testTagsByBlog404()
    {
        $mock404Response = $this->buildMockResponse(404);
        $repo = $this->createTagRepo([
            new ClientException('Error Communicating with Server', new Request('GET', 'test'), $mock404Response),
        ]);
        $result = $repo->getTagsByBlog('blogid', 'projectid', 1, 2, true);

        $this->assertNull($result);
    }
}
