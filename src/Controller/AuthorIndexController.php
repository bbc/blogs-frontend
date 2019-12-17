<?php
declare(strict_types = 1);

namespace App\Controller;

use App\BlogsService\Domain\Author;
use App\BlogsService\Domain\Blog;
use App\BlogsService\Service\AuthorService;
use App\BlogsService\Service\PostService;

class AuthorIndexController extends BlogsBaseController
{
    public function __invoke(Blog $blog, AuthorService $authorService, PostService $postService)
    {
        $page = $this->getPageNumber();

        $authorsResult = $authorService->getAuthorsByBlog($blog, $page);

        /** @var Author[] $authors */
        $authors = $authorsResult->getDomainModels();

        $authorPostResults = $postService->getPostsForAuthors($blog, $authors, 1, 1);

        $paginator = $this->createPaginator($authorsResult);

        $analyticsLabels = $this->atiAnalyticsHelper()->makeLabels('list-authors', 'list-profiles', $blog);
        $pageMetadata = $this->pageMetadataHelper()->makePageMetadata(
            'All authors on the BBC\'s ' . $this->pageMetadataHelper()->blogNameForDescription($blog),
            $blog
        );

        return $this->renderBlogPage(
            'author/index.html.twig',
            $analyticsLabels,
            $pageMetadata,
            $blog,
            [
                'authorPostResults' => $authorPostResults,
                'authors' => $authors,
                'paginatorPresenter' => $paginator,
                'showAZ' => $paginator !== null,
            ]
        );
    }
}
