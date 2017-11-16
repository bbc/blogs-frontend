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
use PHPUnit_Framework_MockObject_MockObject;
use SimpleXMLElement;

class IsiteFeedResponseHandlerTest extends TestCase
{
    /** @var  Mapper | PHPUnit_Framework_MockObject_MockObject*/
    private $mockMapper;

    /** @var  XmlParser | PHPUnit_Framework_MockObject_MockObject */
    private $mockParser;

    /** @var  IsiteFeedResponseHandler */
    private $handler;

    public function testResultQuery()
    {
        $response = $this->setUpQueryResponse('/resultresponse.xml');

        $this->mockMapper
            ->expects($this->atLeastOnce())
            ->method('getDomainModel');

        $result = $this->handler->getIsiteResult($response);

        $this->assertInstanceOf(IsiteResult::class, $result);
    }

    public function testSearchQuery()
    {
        $response = $this->setUpQueryResponse('/searchresponse.xml');

        $this->mockMapper
            ->expects($this->atLeastOnce())
            ->method('getDomainModel');

        $result = $this->handler->getIsiteResult($response);

        $this->assertInstanceOf(IsiteResult::class, $result);
        $this->assertEquals(1, $result->getPage());
        $this->assertEquals(100, $result->getPageSize());
        $this->assertEquals(54, $result->getTotal());
    }

    public function testExceptionOnInvalidXMl()
    {
        $this->mockParser->method('parse')->willThrowException(new ParseException());
        $this->handler = new IsiteFeedResponseHandler($this->mockMapper, $this->mockParser);

        $response = new Response(200, [], 'HERE IS A RANDOM RESPONSE BODY - NOT VALID XML <');

        $this->expectException(IsiteResultException::class);
        $this->expectExceptionMessage('Invalid Isite response body.');

        $this->handler->getIsiteResult($response);
    }

    public function testReturnsEmptyIsiteResultOnNullResponse()
    {
        $this->handler = new IsiteFeedResponseHandler($this->mockMapper, $this->mockParser);

        $result = $this->handler->getIsiteResult(null);

        $this->assertInstanceOf(IsiteResult::class, $result);
        $this->assertEquals([], $result->getDomainModels());
    }

    public function setUp()
    {
        $this->mockMapper = $this->createMock(Mapper::class);
        $this->mockParser = $this->createMock(XmlParser::class);
    }

    private function setUpQueryResponse(string $file): Response
    {
        $xml = file_get_contents(__DIR__ . $file);
        $xmlElementObject = new SimpleXMLElement($xml);

        $this->mockParser->method('parse')->willReturn($xmlElementObject);

        $this->handler = new IsiteFeedResponseHandler($this->mockMapper, $this->mockParser);

        return new Response(200, [], $xml);
    }
}
