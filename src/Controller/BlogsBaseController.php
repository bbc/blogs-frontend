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

    protected function setBlog(Blog $blog)
    {
        $this->analyticsHelper()->setBlog($blog);
        $this->pageContextHelper()->setBlog($blog);
        $this->brandingHelper()->setBrandingId($blog->getBrandingId());
        $this->setLocale($blog->getLanguage());
    }

    protected function renderWithChrome(string $view, array $parameters = [])
    {
        if (!isset($this->blog)) {
            throw new Exception('You smell');
        }

        if (isset($parameters['blogTags'])) {
            throw new Exception('Parameter blogTags should not have already been set');
        }

        if (isset($parameters['blog'])) {
            throw new Exception('Parameter blog should not have already been set');
        }

        if (isset($parameters['modulePresenters'])) {
            throw new Exception('Parameter modulePresenters should not have already been set');
        }


        $parameters['blogTags'] = $this->getTagsByBlog();
        $parameters['blog'] = $this->blog;
        $parameters['modulePresenters'] = $this->getModulePresenters();

        return parent::renderWithChrome($view, $parameters);
    }

    protected function getPageNumber(Request $request): int
    {
        $page = (int) $request->query->get('page', 1);

        return $page > 1 ? $page : 1;
    }

    private function getTagsByBlog(): array
    {
        if ($this->blog === null) {
            throw new Exception('Must set blog using `setBlog()` before calling this method!');
        }

        $tagService = $this->container->get(TagService::class);

        $result = $tagService->getTagsByBlog($this->blog, 1, 18, false);
        $tags = $result->getDomainModels();

        return $tags;
    }

    /**
     * @return Presenter[]
     */
    private function getModulePresenters(): array
    {
        $modulePresenters = [];
        foreach ($this->blog->getModules() as $module) {
            if ($module instanceof FreeText) {
                $modulePresenters[] = new FreetextPresenter($module);
            } elseif ($module instanceof Links) {
                $modulePresenters[] = new LinksPresenter($module);
            }
        }
        return $modulePresenters;
    }
}
