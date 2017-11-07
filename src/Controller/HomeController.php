<?php
declare(strict_types = 1);

namespace App\Controller;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Domain\IsiteEntity;
use App\BlogsService\Service\BlogService;
use Exception;

class HomeController extends BaseController
{
    public function __invoke(BlogService $blogService)
    {
        $blogResult = $blogService->getAllBlogs();
        $blogs = $blogResult->getDomainModels();

        if (count($blogs) == 0) {
            throw new Exception("Blogs is null. Maybe isite is down.");
        }

        $blogsByLetter = $this->getBlogsByLetter($blogs);

        return $this->renderWithChrome('home/show.html.twig', [
            'hasBlogs' => !empty($blogsByLetter),
            'blogsByLetter' => $blogsByLetter,
        ]);
    }

    private function removePrefix($prefix, $str): string
    {
        return trim(preg_replace('/^' . preg_quote($prefix, '/') . '/', '', $str));
    }

    /**
     * @param Blog[]|IsiteEntity[] $blogs
     * @return Blog[]
     */
    private function getBlogsByLetter(array $blogs): array
    {
        $blogsByLetter = [];

        /** @var Blog $blog */
        foreach ($blogs as $blog) {
            if ($blog->getIsArchived()) {
                continue;
            }
            $name = $blog->getName();
            $name = $this->removePrefix('BBC ', $name);
            $name = $this->removePrefix('Blog ', $name);
            $initalLetter = substr($name, 0, 1);
            if (!array_key_exists($initalLetter, $blogsByLetter)) {
                $blogsByLetter[$initalLetter] = [];
            }
            $blogsByLetter[$initalLetter][] = $blog;
        }
        ksort($blogsByLetter);

        return $blogsByLetter;
    }
}
