<?php
declare(strict_types = 1);

namespace App\Controller;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Domain\Module\FreeText;
use App\BlogsService\Domain\Module\Links;
use App\Ds\Presenter;
use App\Ds\SidebarModule\FreetextPresenter;
use App\Ds\SidebarModule\LinksPresenter;
use Exception;

abstract class BlogsBaseController extends BaseController
{
    /** @var Blog */
    private $blog;

    protected function renderWithChrome($view, array $parameters = [])
    {
        if ($this->blog === null) {
            throw new Exception('Must set blog using `setBlog()` before calling this method!');
        }

        if (isset($parameters['modulePresenters'])) {
            throw new Exception('Parameter modulePresenters should not have already been set');
        }

        if (isset($parameters['blog'])) {
            throw new Exception('Parameter blog should not have already been set');
        }

        $parameters['blog'] = $this->blog;
        $parameters['modulePresenters'] = $this->getModulePresenters();

        return parent::renderWithChrome($view, $parameters);
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

    protected function setBlog(Blog $blog)
    {
        $this->blog = $blog;
        $this->setBrandingId($blog->getBrandingId());
    }
}
