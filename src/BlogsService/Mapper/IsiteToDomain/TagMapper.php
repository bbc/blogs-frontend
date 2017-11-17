<?php
declare(strict_types = 1);

namespace App\BlogsService\Mapper\IsiteToDomain;

use App\BlogsService\Domain\IsiteEntity;
use SimpleXMLElement;

class TagMapper extends Mapper
{
    public function getDomainModel(SimpleXMLElement $isiteObject): ?IsiteEntity
    {
        return null;
    }
}
