<?php
declare(strict_types = 1);

namespace App\Ds\SidebarModule;

use App\BlogsService\Domain\Module\Links;
use App\Ds\Presenter;

class LinksPresenter extends Presenter
{
    /** @var Links */
    private $module;

    public function __construct(Links $module, array $options = [])
    {
        parent::__construct($options);

        $this->module = $module;
    }

    public function getLinks(): array
    {
        return $this->module->getLinks();
    }

    public function getTitle(): string
    {
        return $this->module->getTitle();
    }
}
