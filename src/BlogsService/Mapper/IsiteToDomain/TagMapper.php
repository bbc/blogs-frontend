<?php
declare(strict_types = 1);

namespace App\BlogsService\Mapper\IsiteToDomain;

use App\BlogsService\Domain\Tag;
use App\BlogsService\Domain\ValueObject\FileID;
use Exception;
use SimpleXMLElement;

class TagMapper extends Mapper
{
    public function getDomainModel(SimpleXMLElement $isiteObject): ?Tag
    {
        $formMetaData = $this->getFormMetaData($isiteObject);

        if (!is_object($formMetaData)) {
            return null;
        }

        $name = $this->getString($formMetaData->name);

        try {
            $fileId = $this->getString($this->getMetaData($isiteObject)->fileId);
            $fileId = str_replace('blogs-', '', $fileId);

            return new Tag(new FileID($fileId), $name);
        } catch (Exception $e) {
            return null;
        }
    }
}
