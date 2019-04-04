<?php
declare(strict_types = 1);

namespace App\Controller;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Domain\ValueObject\GUID;
use App\BlogsService\Service\PostService;
use App\Service\CommentsService;
use BBC\ProgrammesMorphLibrary\Exception\MorphErrorException;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CommentsPartialController extends BaseController
{
    /** @throws MorphErrorException|Exception */
    public function __invoke(Request $request, Blog $blog, string $guid, PostService $postService, CommentsService $commentsService)
    {
        $response = new Response();
        $response->setPublic()->setMaxAge(10);

        $post = $postService->getPostByGuid(new GUID($guid), $this->isPreview($request), $blog);

        if (!$post) {
            throw $this->createNotFoundException('Post not found');
        }

        $commentsPromise = $blog->hasCommentsEnabled() ? $commentsService->getByBlogAndPost($blog, $post) : null;
        $comments = $commentsPromise ? $commentsPromise->wait() : null;

        return $this->render('comments/partial.html.twig', ['comments' => $comments], $response);
    }
}
