<?php
declare(strict_types = 1);

namespace App\BlogsService\Domain\ContentBlock;

abstract class AbstractContentBlock
{
    public function getCharacterCount(): int
    {
        return 200; // items take up 200 characters by default
    }
}
