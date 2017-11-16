<?php
declare(strict_types = 1);
namespace App\BlogsService\Domain\ValueObject;

use InvalidArgumentException;

class FileID
{
    /** @var string */
    private $fileId;

    public function __construct(string $fileId)
    {
        if (!$this->isValidFileId($fileId)) {
            throw new InvalidArgumentException('The FileID supplied is invalid!');
        }

        $this->fileId = $fileId;
    }

    public function __toString(): string
    {
        return $this->fileId;
    }

    private function isValidFileId(string $fileId): bool
    {
        return !!preg_match('/^[A-Za-z0-9-]*$/', $fileId);
    }
}
