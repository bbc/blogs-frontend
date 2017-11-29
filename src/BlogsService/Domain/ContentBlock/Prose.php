<?php
declare(strict_types = 1);

namespace App\BlogsService\Domain\ContentBlock;

class Prose extends AbstractContentBlock
{
    /** @var string */
    private $prose;

    /** @var int */
    private $charCount;

    public function __construct(string $prose)
    {
        $this->prose = $prose;
    }

    public function getCharacterCount(): int
    {
        if (!isset($this->charCount)) {
            $this->charCount = mb_strlen(strip_tags($this->prose));
        }

        return $this->charCount;
    }

    public function getProse(): string
    {
        return $this->prose;
    }
}
