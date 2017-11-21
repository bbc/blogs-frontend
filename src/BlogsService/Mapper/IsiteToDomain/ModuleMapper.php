<?php
declare(strict_types = 1);

namespace App\BlogsService\Mapper\IsiteToDomain;

use App\BlogsService\Domain\Module\FreeText;
use App\BlogsService\Domain\Module\Links;
use App\BlogsService\Domain\Module\Module;
use Exception;
use SimpleXMLElement;

class ModuleMapper extends Mapper
{
    /**
     * @param SimpleXMLElement $isiteObject
     * @return Module
     * @throws Exception
     */
    public function getDomainModel(SimpleXMLElement $isiteObject)
    {
        $type = str_replace('blogs-sidebar-', '', $this->getMetaData($isiteObject)->type);
        $form = $this->getForm($isiteObject);

        if ($type === 'freetext') {
            $moduleData = $form->{'freetext'};

            return new FreeText(
                $this->getString($moduleData->{'moduletitle'}),
                $this->getString($moduleData->{'body'}),
                $this->getImageIfExists($moduleData->{'image'})
            );
        }

        if ($type === 'links') {
            // @codingStandardsIgnoreStart
            $links = $form->Links->xpath('./*');
            // @codingStandardsIgnoreEnd

            $heading = array_shift($links);
            $linksArray = [];
            foreach ($links as $link) {
                $linksArray[] = array_map('trim', (array) $link);
            }

            return new Links(
                $this->getString($heading),
                $linksArray
            );
        }

        throw new Exception('Invalid Module Type : ' . $type);
    }
}
