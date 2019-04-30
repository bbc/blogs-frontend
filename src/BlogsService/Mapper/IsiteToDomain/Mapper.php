<?php
declare(strict_types = 1);

namespace App\BlogsService\Mapper\IsiteToDomain;

use App\BlogsService\Domain\Image;
use App\BlogsService\Infrastructure\MapperFactory;
use Cake\Chronos\Chronos;
use Psr\Log\LoggerInterface;
use SimpleXMLElement;

/**
 *  This is the base mapper for the blog which provides the basic
 *  functionality such as creating new instances of the same model
 *  setting and getting values etc.
 */

abstract class Mapper
{
    protected $mapperFactory;

    protected $logger;

    public function __construct(MapperFactory $mapperFactory, LoggerInterface $logger)
    {
        $this->mapperFactory = $mapperFactory;
        $this->logger = $logger;
    }

    abstract public function getDomainModel(SimpleXMLElement $isiteObject);

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
    protected function getFormMetaData(SimpleXMLElement $isiteObject): ?SimpleXMLElement
    {
        return $this->getForm($isiteObject)->metadata;
    }

    protected function getUserInputDateTime(SimpleXMLElement $date): Chronos
    {
        $date = $this->getString($date);
        // the published-date field we use is auto-generated in iSite but can be modified by the user.
        // That means that depending on
        //  a) whether the user edits the field
        //  b) the user's browser
        //  c) the mood of cthulu
        // this field may or may not contain a timezone, which may or may not be correct
        // So we strip timezone from the ISO responses that iSite gives us and assume the user meant europe/london
        // which they nearly always do.
        $date = preg_replace('/[Z\+].*$/', '', $date);
        return new Chronos($date, 'Europe/London');
    }

    protected function getImage($pid): Image
    {
        $pid = (string) $pid;
        if (empty($pid)) {
            //Default image if no image was present
            $pid = 'p0215q0b';
        }
        return new Image($pid);
    }

    protected function getImageIfExists($pid): ?Image
    {
        $pid = (string) $pid;
        if (empty($pid)) {
            return null;
        }

        return $this->getImage($pid);
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
