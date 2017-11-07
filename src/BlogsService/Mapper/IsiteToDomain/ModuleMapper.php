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
//        $type       = str_replace("blogs-sidebar-", "", $this->getMetaData($isiteObject)->type);
//        $metadata   = $this->getFormMetaData($isiteObject);
//        $form       = $this->getForm($isiteObject);
//
//        $module = null;
//        switch ($type) {
//            case 'freetext':
//                $moduleData   = $form->{'freetext'};
//
//                $module = new FreeText(
//                    $type,
//                    $this->getString($moduleData->{'moduletitle'}),
//                    $this->getString($moduleData->{'body'}),
//                    $this->getImageIfExists($moduleData->{'image'})
//                );
//                break;
//            case 'links':
//                $links  = $form->Links->xpath("./*");
//
//                $linksCount = count($links);
//                for ($i = 0; $i < $linksCount; $i++) {
//                    //Conversion of array to object : http://stackoverflow.com/questions/1869091/how-to-convert-an-array-to-object-in-php
//                    $links[$i] = json_decode(json_encode((array) $links[$i], FALSE));
//                }
//
//                $module = new Links(
//                    $type,
//                    $this->getString($form->metadata->name),
//                    $links
//                );
//                break;
//            default:
//                throw new Exception("Invalid Module Type : " . $type);
//        }
//
//        return $module;
    }
}
