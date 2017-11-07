<?php
declare(strict_types = 1);

namespace Tests\App\BlogsService\Infrastructure\IsiteFeedResponseHandler;

use App\BlogsService\Infrastructure\Exception\ParseException;
use App\BlogsService\Infrastructure\IsiteFeedResponseHandler;
use App\BlogsService\Infrastructure\IsiteResult;
use App\BlogsService\Infrastructure\IsiteResultException;
use App\BlogsService\Infrastructure\XmlParser;
use App\BlogsService\Mapper\IsiteToDomain\Mapper;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use SimpleXMLElement;

class IsiteFeedResponseHandlerTest extends TestCase
{
    /** TODO test parses search type response correctly */
    public function testResultQuery()
    {
        $xml = file_get_contents(__DIR__ . '/resultresponse.xml');
        $xmlElementObject = new SimpleXMLElement($xml);

        $mockMapper = $this->createMock(Mapper::class);
        $mockParser = $this->createMock(XmlParser::class);
        $mockParser->method('parse')->willReturn($xmlElementObject);

        $handler = new IsiteFeedResponseHandler($mockMapper, $mockParser);

        $response = new Response(200, [], $xml);

        $mockMapper
            ->expects($this->atLeastOnce())
            ->method('getDomainModel');

        $result = $handler->getIsiteResult($response);

        $this->assertInstanceOf(IsiteResult::class, $result);
    }

    public function testSearchQuery()
    {
        $xml = file_get_contents(__DIR__ . '/searchresponse.xml');
        $xmlElementObject = new SimpleXMLElement($xml);

        $mockMapper = $this->createMock(Mapper::class);
        $mockParser = $this->createMock(XmlParser::class);
        $mockParser->method('parse')->willReturn($xmlElementObject);

        $handler = new IsiteFeedResponseHandler($mockMapper, $mockParser);

        $response = new Response(200, [], $xml);

        $mockMapper
            ->expects($this->atLeastOnce())
            ->method('getDomainModel');

        $result = $handler->getIsiteResult($response);

        $this->assertInstanceOf(IsiteResult::class, $result);
        $this->assertEquals(1, $result->getPage());
        $this->assertEquals(100, $result->getPageSize());
        $this->assertEquals(54, $result->getTotal());
    }

    public function testExceptionOnInvalidXMl()
    {
        $mockMapper = $this->createMock(Mapper::class);
        $mockParser = $this->createMock(XmlParser::class);
        $mockParser->method('parse')->willThrowException(new ParseException());

        $handler = new IsiteFeedResponseHandler($mockMapper, $mockParser);

        $response = new Response(200, [], 'HERE IS A RANDOM RESPONSE BODY - NOT VALID XML <');

        $this->expectException(IsiteResultException::class);
        $this->expectExceptionMessage('Invalid Isite response body.');

        $result = $handler->getIsiteResult($response);
    }

    public function testReturnsEmptyIsiteResultOnNullResponse()
    {
        $mockMapper = $this->createMock(Mapper::class);
        $mockParser = $this->createMock(XmlParser::class);
        $handler = new IsiteFeedResponseHandler($mockMapper, $mockParser);

        $result = $handler->getIsiteResult(null);

        $this->assertInstanceOf(IsiteResult::class, $result);
        $this->assertEquals([], $result->getDomainModels());
    }
}
