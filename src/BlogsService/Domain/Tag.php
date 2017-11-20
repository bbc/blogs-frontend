<?php
declare(strict_types = 1);
namespace App\BlogsService\Domain;

use App\BlogsService\Domain\ValueObject\FileID;

class Tag
{
    /** @var FileID */
    private $fileId;

    public function __construct()
    {
    }

    public function getFileId(): FileID
    {
        return $this->fileId;
    }
}
