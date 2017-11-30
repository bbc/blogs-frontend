<?php
declare(strict_types = 1);

namespace App\BlogsService\Domain\ContentBlock;

abstract class AbstractContentBlock
{
    abstract public function getCharacterCount(): int;
}
