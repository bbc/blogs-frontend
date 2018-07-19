<?php
declare(strict_types = 1);

namespace App\BlogsService\Domain\ContentBlock;

class Prose extends AbstractContentBlock
{
    /** @var string */
    private $prose;

    /** @var int|null */
    private $charCount;

    public function __construct(string $prose)
    {
        $this->prose = $prose;
    }

    public function getCharacterCount(): int
    {
        if ($this->charCount === null) {
            $this->charCount = mb_strlen(strip_tags($this->prose)) ?: 0;
//            $this->charCount = $this->charCount ? $this->charCount : 0;
        }

        return $this->charCount;
    }

    public function getProse(): string
    {
        return $this->prose;
    }
}
