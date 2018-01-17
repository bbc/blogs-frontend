<?php
declare(strict_types = 1);

namespace App\Controller;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Domain\ValueObject\GUID;
use App\BlogsService\Service\PostService;
use App\Service\CommentsService;
use BBC\ProgrammesMorphLibrary\Exception\MorphErrorException;
use Exception;

class PostShowController extends BlogsBaseController
{
    /** @throws MorphErrorException|Exception */
    public function __invoke(Blog $blog, string $guid, PostService $postService, CommentsService $commentsService)
    {
        $this->setBlog($blog);

        $post = $postService->getPostByGuid(new GUID($guid), $blog);
        $commentsPromise = $blog->hasCommentsEnabled() ? $commentsService->getByBlogAndPostAsync($blog, $post) : null;

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

        [$previousPost, $nextPost] = $postService->getPreviousAndNextPosts($blog, $post->getPublishedDate());

        $comments = $commentsPromise ? $commentsPromise->wait() : null;

        return $this->renderWithChrome(
            'post/show.html.twig',
            [
                'post' => $post,
                'prevPost' => $previousPost,
                'nextPost' => $nextPost,
                'comments' => $comments,
            ]
        );
    }

    protected function getIstatsPageType(): string
    {
        return 'post_show';
    }
}
