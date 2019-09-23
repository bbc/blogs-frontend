<?php

declare(strict_types=1);

namespace App\BlogsService\Domain\ContentBlock;

class CodePen extends AbstractContentBlock
{
    /** @var string  */
    private $codepen;

    public function __construct(string $codepenId)
    {
        $this->codepen = $codepenId;
    }

    public function getCodePen(): string
    {
        return $this->codepen;
    }
}
