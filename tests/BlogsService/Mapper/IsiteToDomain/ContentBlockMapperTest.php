<?php
namespace Tests\App\BlogsService\Mapper\IsiteToDomain;

use App\BlogsService\Domain\ContentBlock\Image;
use App\BlogsService\Infrastructure\MapperFactory;
use App\BlogsService\Mapper\IsiteToDomain\ContentBlockMapper;
use Psr\Log\LoggerInterface;
use SimpleXMLElement;
use PHPUnit\Framework\TestCase;

class ContentBlockMapperTest extends TestCase
{
    /** @var ContentBlockMapper */
    private $mapper;

    public function setUp()
    {
        $dummyLogger = $this->createMock(LoggerInterface::class);

        $this->mapper = new ContentBlockMapper(
            new MapperFactory($dummyLogger),
            $dummyLogger
        );
    }

    /**
     * @dataProvider xmlResponsesProvider
     */
    public function testCanMapImagesWithoutOptionalFields(string $fileNameWithResponse)
    {
        $xmlResponse = $this->givenXmlResponseFromIsite($fileNameWithResponse);

        $mapped = $this->mapper->getDomainModel($xmlResponse);

        $this->assertInstanceOf(Image::class, $mapped);
    }

    public function xmlResponsesProvider()
    {
        return [
            'Image with all fields filled' => ['imageWithAllFieldsFilled.xml'],
            'image with optional CAPTION field not filled' => ['imageWithoutCaption.xml'],
        ];
    }

    private function givenXmlResponseFromIsite(string $file): SimpleXMLElement
    {
        $xml = file_get_contents(__DIR__ . '/ContentBlockMapperResponses/' . $file);
        return new SimpleXMLElement($xml);
    }
}
