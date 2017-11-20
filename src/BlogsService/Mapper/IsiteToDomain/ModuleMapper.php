<?php
declare(strict_types = 1);

namespace App\BlogsService\Mapper\IsiteToDomain;

use App\BlogsService\Domain\Module\FreeText;
use Exception;
use SimpleXMLElement;

class ModuleMapper extends Mapper
{
    /**
     * @param SimpleXMLElement $isiteObject
     * @return FreeText
     * @throws Exception
     */
    public function getDomainModel(SimpleXMLElement $isiteObject)
    {
        $type = str_replace('blogs-sidebar-', '', $this->getMetaData($isiteObject)->type);
        $form = $this->getForm($isiteObject);

        switch ($type) {
            case 'freetext':
                $moduleData = $form->{'freetext'};

                return new FreeText(
                    $this->getString($moduleData->{'moduletitle'}),
                    $this->getString($moduleData->{'body'}),
                    $this->getImageIfExists($moduleData->{'image'})
                );
                break;
//            case 'links':
//                // None of the blogs seem to current have links. So this shouldn't break any pages....
            default:
                throw new Exception('Invalid Module Type : ' . $type); //@TODO should this be inside or outside of the switch statement?
        }
    }
}
