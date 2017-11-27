<?php
declare(strict_types = 1);

namespace App\Ds\ContentBlock\ProseBlock;

use App\BlogsService\Domain\ContentBlock\Prose;
use App\Ds\Presenter;

class ProseBlockPresenter extends Presenter
{
    /** @var Prose */
    private $content;

    public function __construct(Prose $content, array $options = [])
    {
        parent::__construct($options);
        $this->content = $content;
    }
}
