<?php
declare(strict_types = 1);

namespace App\BlogsService\Mapper\IsiteToDomain;

use phpDocumentor\Reflection\DocBlock\Tag;
use SimpleXMLElement;

class TagMapper extends Mapper
{
    public function getDomainModel(SimpleXMLElement $isiteObject): ?Tag
    {
        return null;
    }
}
