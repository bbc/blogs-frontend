<?php
declare(strict_types = 1);

namespace App\Ds\Module;

use App\BlogsService\Domain\Module\FreeText;
use App\Ds\Presenter;

class FreetextPresenter extends Presenter
{
    /** @var FreeText */
    private $module;

    public function __construct(FreeText $module, array $options = [])
    {
        parent::__construct($options);

        $this->module = $module;
    }

    public function getBody(): string
    {
        return $this->module->getBody();
    }

    public function getImageUrl($width): string
    {
        return $this->module->getImage()->getUrl($width);
    }

    public function getTitle(): string
    {
        return $this->module->getTitle();
    }

    public function hasImage(): bool
    {
        return $this->module->getImage() !== null;
    }
}
