<?php
declare(strict_types = 1);

namespace App\BlogsService\Mapper\IsiteToDomain;

use App\BlogsService\Domain\Image;
use App\BlogsService\Domain\IsiteEntity;
use App\BlogsService\Infrastructure\MapperFactory;
use DateTimeImmutable;
use SimpleXMLElement;

/**
 *  This is the base mapper for the blog which provides the basic
 *  functionality such as creating new instances of the same model
 *  setting and getting values etc.
 */

abstract class Mapper
{
    protected $mapperFactory;

    public function __construct(MapperFactory $mapperFactory)
    {
        $this->mapperFactory = $mapperFactory;
    }

    abstract public function getDomainModel(SimpleXMLElement $isiteObject): ?IsiteEntity;

    protected function getForm(SimpleXMLElement $isiteObject): SimpleXMLElement
    {
        return $isiteObject->document->form;
    }

    /**
     * Gets the metadata directly from the header
     * @param  SimpleXMLElement $isiteObject
     * @return SimpleXMLElement
     */
    protected function getMetaData(SimpleXMLElement $isiteObject): SimpleXMLElement
    {
        return $isiteObject->metadata;
    }

    /**
     * Method to get the metadata form isite document
     * @param  SimpleXMLElement $isiteObject
     * @return SimpleXMLElement
     */
    protected function getFormMetaData(SimpleXMLElement $isiteObject): SimpleXMLElement
    {
        return $this->getForm($isiteObject)->metadata;
    }

    protected function getDate($date): DateTimeImmutable
    {
        return new DateTimeImmutable($date);
    }

    protected function getImage(SimpleXMLElement $pid): Image
    {
        $pid = $this->getString($pid);
        if (empty($pid)) {
            //Default image if no image was present
            $pid = 'p0215q0b';
        }
        return new Image($pid);
    }

    protected function getImageIfExists($pid): ?Image
    {
        $pid = (string) $pid;
        if (!empty($pid)) {
            return $this->getImage($pid);
        }
        return null;
    }

    protected function getString(?SimpleXMLElement $val): ?string
    {
        if (empty($val)) {
            return null;
        }
        $val = (string) $val;
        return trim($val);
    }

    protected function getBoolean(?SimpleXMLElement $key): bool
    {
        return $this->getString($key) == "true";
    }
}
