<?php
declare(strict_types = 1);

namespace App\BlogsService\Mapper\IsiteToDomain;

use App\BlogsService\Domain\Module\FreeText;
use App\BlogsService\Domain\Module\Links;
use App\BlogsService\Domain\Module\ModuleInterface;
use Exception;
use SimpleXMLElement;

class ModuleMapper extends Mapper
{
    /**
     * @param SimpleXMLElement $isiteObject
     * @return ModuleInterface
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
            $linksArray = [];
            //@codingStandardsIgnoreStart
            foreach ($form->Links->links as $link) {
                $linksArray[] = array_map('trim', (array) $link);
            }
            return new Links(
                $this->getString($form->Links->{'moduletitle'}),
                $linksArray
            );
            //@codingStandardsIgnoreEnd
        }

        throw new Exception('Invalid Module Type : ' . $type);
    }
}
