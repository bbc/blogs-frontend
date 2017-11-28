<?php
declare(strict_types = 1);

namespace App\BlogsService\Domain\ContentBlock;

class Code extends AbstractContentBlock
{
    /** @var string  */
    private $code;

    public function __construct(string $code)
    {
        $this->code = $code;
    }

    public function getCharacterCount(): int
    {
        $code = $this->code;
        return mb_strlen($code);
    }

    public function getCode(): string
    {
        return $this->code;
    }
}
