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

        $post = $postService->getPostByGuid(new GUID($guid), false, $blog);

        if (!$post) {
            throw $this->createNotFoundException('Post not found');
        }

        $commentsPromise = $commentsService->getByBlogAndPost($blog, $post);

        $comments = $commentsPromise->wait();

        $analyticsLabels = $this->atiAnalyticsHelper()->makeLabels('comments', $blog, $post->hasVideo());
        $pageMetadata = $this->pageMetadataHelper()->makePageMetadata(
            $post->getShortSynopsis() ?: $blog->getDescription(),
            $blog,
            $post->getImage()
        );

        $colours = $this
            ->brandingHelper()
            ->requestBranding($blog->getBrandingId())
            ->getColours();

        $this->response()->setPublic()->setMaxAge(10);

        return $this->renderBlogPage(
            'comments/index.html.twig',
            $analyticsLabels,
            $pageMetadata,
            $blog,
            [
                'post' => $post,
                'comments' => $comments,
                'colours' => $colours,
            ]
        );
    }
}
