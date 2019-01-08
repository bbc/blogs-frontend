<?php
declare(strict_types = 1);

namespace App\Controller;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Domain\ValueObject\GUID;
use App\BlogsService\Service\PostService;
use App\Service\CommentsService;
use BBC\ProgrammesMorphLibrary\Exception\MorphErrorException;
use Exception;

class CommentsIndexController extends BlogsBaseController
{
    /** @throws MorphErrorException|Exception */
    public function __invoke(Blog $blog, string $guid, PostService $postService, CommentsService $commentsService)
    {
        if ($blog->hasCommentsEnabled() == false) {
            throw $this->createNotFoundException('Comments are not enabled for this blog');
        }

        $this->setBlog($blog);

        $post = $postService->getPostByGuid(new GUID($guid), $blog);

        if (!$post) {
            throw $this->createNotFoundException('Post not found');
        }

        $commentsPromise = $commentsService->getByBlogAndPost($blog, $post);

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

        $comments = $commentsPromise->wait();

        $this->response()->setPrivate();

        return $this->renderWithChrome(
            'comments/index.html.twig',
            [
                'post' => $post,
                'comments' => $comments,
            ]
        );
    }

    protected function getIstatsPageType(): string
    {
        return 'comments_index';
    }
}
