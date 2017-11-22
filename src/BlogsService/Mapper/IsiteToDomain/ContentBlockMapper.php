<?php
declare(strict_types = 1);

namespace App\BlogsService\Mapper\IsiteToDomain;

use SimpleXMLElement;

class ContentBlockMapper extends Mapper
{
    public function getDomainModel(SimpleXMLElement $isiteObject)
    {
        return null;
    }
}
