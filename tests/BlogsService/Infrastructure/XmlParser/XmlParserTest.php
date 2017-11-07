<?php
declare(strict_types = 1);

namespace Tests\App\BlogsService\Infrastructure\XmlParser;

use App\BlogsService\Infrastructure\XmlParser;
use PHPUnit\Framework\TestCase;
use SimpleXMLElement;

class XmlParserTest extends TestCase
{
    public function testValidXml()
    {
        $xml = file_get_contents(__DIR__ . '/electron_eastenders.xml');
        $parser = new XmlParser();
        $result = $parser->parse($xml);
        $this->assertInstanceOf(SimpleXMLElement::class, $result);
        $this->assertEquals('The Queen Vic Jukebox on BBC Music', (string) $result->content->pages->page->title);
    }

    /**
     * @expectedException \App\BlogsService\Infrastructure\Exception\ParseException
     * @expectedExceptionMessageRegExp /Unable to parse XML: String could not be parsed as XML. LIBXMLError: XML error: "expected '>' " \(level 3\) \(Code 73\) on line 25/
     */
    public function testInvalidXml()
    {
        $xml = file_get_contents(__DIR__ . '/invalid.xml');
        $parser = new XmlParser();
        $parser->parse($xml);
    }

    /**
     * @expectedException \App\BlogsService\Infrastructure\Exception\ParseException
     */
    public function testStupidXml()
    {
        $xml = file_get_contents(__DIR__ . '/stupid.xml');
        $parser = new XmlParser();
        $parser->parse($xml);
    }
}
