<?php
declare(strict_types = 1);

namespace App\Controller;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Domain\ValueObject\GUID;
use App\BlogsService\Service\AuthorService;
use App\BlogsService\Service\PostService;
use Symfony\Component\HttpFoundation\Request;

class AuthorShowController extends BlogsBaseController
{
    public function __invoke(Blog $blog, string $guid, AuthorService $authorService, PostService $postService)
    {
        $this->pageMetadataHelper()->setAllowPreview();
        $author = $authorService->getAuthorByGUID(new GUID($guid), $blog, $this->pageMetadataHelper()->isPreview());

        if (!$author) {
            throw $this->createNotFoundException('Author not found');
        }

        $page = $this->getPageNumber();

        $postResult = $postService->getPostsByAuthor($blog, $author, $page);

        $paginator = $this->createPaginator($postResult);

        $pageMetadata = $this->pageMetadataHelper()->makePageMetadata(
            'All posts on the ' . $this->pageMetadataHelper()->blogNameForDescription($blog) . ' by ' . $author->getName(),
            $blog,
            $author->getImage()
        );

        $analyticsLabels = $this->atiAnalyticsHelper()->makeLabels('author', $blog);

        return $this->renderBlogPage(
            'author/show.html.twig',
            $analyticsLabels,
            $pageMetadata,
            $blog,
            [
                'author' => $author,
                'paginatorPresenter' => $paginator,
                'postResult' => $postResult,
            ]
        );
    }
}
