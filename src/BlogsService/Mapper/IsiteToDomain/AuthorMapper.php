<?php
declare(strict_types = 1);

namespace App\BlogsService\Mapper\IsiteToDomain;

use App\BlogsService\Domain\Author;
use SimpleXMLElement;

class AuthorMapper extends Mapper
{
    public function getDomainModel(SimpleXMLElement $isiteObject): ?Author
    {
        return null;
    }
}
