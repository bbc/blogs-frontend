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
        $this->pageMetadataHelper()->setAllowPreview();
        $post = $postService->getPostByGuid(new GUID($guid), $this->pageMetadataHelper()->isPreview(), $blog);

        if (!$post) {
            throw $this->createNotFoundException('Post not found');
        }

        $commentsPromise = $blog->hasCommentsEnabled() ? $commentsService->getByBlogAndPost($blog, $post) : null;

        [$previousPost, $nextPost] = $postService->getPreviousAndNextPosts($blog, $post->getPublishedDate());

        $comments = $commentsPromise ? $commentsPromise->wait() : null;

        $analyticsLabels = $this->atiAnalyticsHelper()->makeLabels('post', $blog, $post->hasVideo());
        $pageMetadata = $this->pageMetadataHelper()->makePageMetadata(
            $post->getShortSynopsis() ? $post->getShortSynopsis() : $blog->getDescription(),
            $blog,
            $post->getImage()
        );

        return $this->renderBlogPage(
            'post/show.html.twig',
            $analyticsLabels,
            $pageMetadata,
            $blog,
            [
                'post' => $post,
                'prevPost' => $previousPost,
                'nextPost' => $nextPost,
                'comments' => $comments,
            ]
        );
    }
}
