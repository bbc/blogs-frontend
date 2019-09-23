<?php

declare(strict_types=1);

namespace App\Ds\Post\ContentBlock\CodePenBlock;

use App\BlogsService\Domain\ContentBlock\CodePen;
use App\Ds\Presenter;

class CodePenBlockPresenter extends Presenter
{
    /** @var int|null */
    private $charLimit;

    /** @var Code */
    private $content;

    public function __construct(CodePen $content, array $options = [])
    {
        parent::__construct($options);
        $this->content = $content;
    }

    public function getCodePen(): string
    {
        return $this->content->getCodePen();
    }
}
