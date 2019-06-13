<?php
declare(strict_types = 1);

namespace App\Controller;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Domain\Module\FreeText;
use App\BlogsService\Domain\Module\Links;
use App\BlogsService\Infrastructure\IsiteResult;
use App\BlogsService\Service\TagService;
use App\Controller\Helpers\ValueObjects\PageContext;
use App\Ds\Molecule\Paginator\PaginatorPresenter;
use App\Ds\Presenter;
use App\Ds\SidebarModule\FreetextPresenter;
use App\Ds\SidebarModule\LinksPresenter;
use Exception;
use Symfony\Component\HttpFoundation\Request;

abstract class BlogsBaseController extends BaseController
{
    /** @var Blog */
    private $blog;

    public static function getSubscribedServices()
    {
        return array_merge(parent::getSubscribedServices(), [
            TagService::class,
        ]);
    }

    protected function createPaginator(IsiteResult $result): ?PaginatorPresenter
    {
        if ($result->getTotal() > $result->getPageSize()) {
            return new PaginatorPresenter($result->getPage(), $result->getPageSize(), $result->getTotal());
        }

        return null;
    }

    protected function renderWithChrome(PageContext $pageContext, Blog $blog, string $view, array $parameters = [])
    {
        if (isset($parameters['blogTags'])) {
            throw new Exception('Parameter blogTags should not have already been set');
        }

        if (isset($parameters['blog'])) {
            throw new Exception('Parameter blog should not have already been set');
        }

        if (isset($parameters['modulePresenters'])) {
            throw new Exception('Parameter modulePresenters should not have already been set');
        }
        $this->setLocale($blog->getLanguage());
        $this->brandingHelper()->setBrandingId($blog->getBrandingId());
        $parameters['blogTags'] = $this->getTagsByBlog($blog);
        $parameters['blog'] = $blog;
        $parameters['modulePresenters'] = $this->getModulePresenters($blog);

        return parent::renderWithChrome($view, $parameters);
    }

    protected function getPageNumber(Request $request): int
    {
        $page = (int) $request->query->get('page', 1);

        return $page > 1 ? $page : 1;
    }

    private function getTagsByBlog(Blog $blog): array
    {
        if ($blog === null) {
            throw new Exception('Must set blog using `setBlog()` before calling this method!');
        }

        $tagService = $this->container->get(TagService::class);

        $result = $tagService->getTagsByBlog($blog, 1, 18, false);
        $tags = $result->getDomainModels();

        return $tags;
    }

    /**
     * @param  Blog $blog
     * @return Presenter[]
     */
    private function getModulePresenters(Blog $blog): array
    {
        $modulePresenters = [];
        foreach ($blog->getModules() as $module) {
            if ($module instanceof FreeText) {
                $modulePresenters[] = new FreetextPresenter($module);
            } elseif ($module instanceof Links) {
                $modulePresenters[] = new LinksPresenter($module);
            }
        }
        return $modulePresenters;
    }
}
