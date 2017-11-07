<?php
declare(strict_types = 1);

namespace App\BlogsService\Infrastructure;

use App\BlogsService\Infrastructure\Exception\ParseException;
use SimpleXMLElement;
use LibXMLError;

class XmlParser
{
    public function parse(string $body, array $config = []): SimpleXMLElement
    {
        if (!$body) {
            throw new ParseException("Empty XML Body");
        }
        $defaultConfig = [
            'options' => LIBXML_NONET,
            'ns' => '',
            'ns_is_prefix' => false,
        ];
        $config = array_merge($defaultConfig, $config);
        $oldDisableEntities = libxml_disable_entity_loader(true);
        $oldInternalErrors = libxml_use_internal_errors(true);
        libxml_clear_errors();

        try {
            $xml = new SimpleXMLElement(
                $body,
                $config['options'],
                false,
                $config['ns'],
                $config['ns_is_prefix']
            );
        } catch (\Exception $e) {
            $libXmlError = libxml_get_last_error();
            $libXmlErrorString = $libXmlError ? $this->libXmlErrorToString($libXmlError) : '';
            throw new ParseException('Unable to parse XML: ' . $e->getMessage() . '. LIBXMLError: ' . $libXmlErrorString);
        } finally {
            libxml_disable_entity_loader($oldDisableEntities);
            libxml_use_internal_errors($oldInternalErrors);
        }

        return $xml;
    }

    private function libXmlErrorToString(LibXMLError $error)
    {
        return sprintf(
            'XML error: "%s" (level %d) (Code %d) on line %d column %d',
            str_replace("\n", " ", $error->message),
            $error->level,
            $error->code,
            $error->line,
            $error->column
        );
    }
}
