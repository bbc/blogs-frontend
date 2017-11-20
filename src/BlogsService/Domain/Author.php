<?php
declare(strict_types = 1);
namespace App\BlogsService\Domain;

use App\BlogsService\Domain\ValueObject\FileID;
use App\BlogsService\Domain\ValueObject\GUID;

class Author
{
    /** @var GUID */
    private $guid;

    /** @var FileID */
    private $fileId;

    public function __construct()
    {
    }

    public function getGuid(): GUID
    {
        return $this->guid;
    }

    public function getFileId(): FileID
    {
        return $this->fileId;
    }
}
