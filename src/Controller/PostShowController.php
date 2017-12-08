<?php
declare(strict_types = 1);

namespace App\Controller;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Domain\ValueObject\GUID;
use App\BlogsService\Service\PostService;
use Cake\Chronos\Chronos;

class PostShowController extends BlogsBaseController
{
    public function __invoke(Blog $blog, string $guid, PostService $postService)
    {
        $this->setBlog($blog);

        $post = $postService->getPostByGuid(new GUID($guid), $blog);

        if (!$post) {
            throw $this->createNotFoundException('Post not found');
        }

        $this->hasVideo = $post->hasVideo();
        $this->counterName = $post->getPublishedDate()->format('Y') . '.' . $post->getPublishedDate()->format('m') . '.post.' . $post->getTitle();

        $istatsLabels = [
            'post_title' => $post->getTitle(),
            'published_date' => $post->getPublishedDate()->format('F j, Y, g:i a'),
        ];
        if ($post->getAuthor() !== null) {
            $istatsLabels['post_author'] = $post->getAuthor()->getName();
        }

        $this->otherIstatsLabels = $istatsLabels;

        $previousPost = $postService->getPostsBefore(
            $blog,
            $post->getPublishedDate()
        );

        $nextPost = $postService->getPostsAfter(
            $blog,
            $post->getPublishedDate(),
            Chronos::now()
        );

        return $this->renderWithChrome(
            'post/show.html.twig',
            [
                'post' => $post,
                'prevPost' => $previousPost,
                'nextPost' => $nextPost,
            ]
        );
    }

    protected function getIstatsPageType(): string
    {
        return 'post_show';
    }
}
