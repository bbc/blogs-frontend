<?php
declare(strict_types = 1);

namespace Tests\App\Controller;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Domain\Post;
use App\BlogsService\Service\BlogService;
use App\BlogsService\Service\PostService;
use Tests\App\BaseWebTestCase;

/**
 * @covers \App\Controller\PostShowController
 */
class PostShowControllerTest extends BaseWebTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->setupBlog();
        $this->setTestTags();
    }

    public function testNonExistentPost()
    {
        $this->setupPost();
        $this->client->request('GET', '/blogs/strictlycomedancing/entries/a8479d24-d3b8-3b00-9b36-e4636f9616f7');

        $this->assertResponseStatusCode($this->client, 404);
    }

    private function setupPost(?Post $post = null)
    {
        $postService = $this->createMock(PostService::class);
        $postService
            ->expects($this->once())
            ->method('getPostByGuid')
            ->willReturn($post);

        $this->client->getContainer()->set(PostService::class, $postService);
    }

    private function setupBlog()
    {
        $blog = $this->createMock(Blog::class);
        $blog->method('hasCommentsEnabled')->willReturn(true);

        $blogService = $this->createMock(BlogService::class);
        $blogService->method('getBlogById')->willReturn($blog);

        $this->client->getContainer()->set(BlogService::class, $blogService);
    }
}
