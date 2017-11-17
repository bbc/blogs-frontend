<?php
namespace App\BlogsService\Mapper\IsiteToDomain;

//use App\BlogsService\Domain\Module\FreeText;
//use App\BlogsService\Domain\Module\Links;
//use Exception;

use App\BlogsService\Domain\IsiteEntity;
use SimpleXMLElement;

class ModuleMapper extends Mapper
{
    public function getDomainModel(SimpleXMLElement $isiteObject): ?IsiteEntity
    {
        return null;
    }
}
