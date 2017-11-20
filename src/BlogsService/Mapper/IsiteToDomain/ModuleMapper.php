<?php
namespace App\BlogsService\Mapper\IsiteToDomain;

use App\BlogsService\Domain\Module;
use SimpleXMLElement;

class ModuleMapper extends Mapper
{
    public function getDomainModel(SimpleXMLElement $isiteObject): ?Module
    {
        return null;
    }
}
