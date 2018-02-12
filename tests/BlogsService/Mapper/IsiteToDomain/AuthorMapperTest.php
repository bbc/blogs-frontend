<?php
declare(strict_types = 1);

namespace Tests\App\BlogsService\Mapper\IsiteToDomain;

use App\BlogsService\Infrastructure\MapperFactory;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use SimpleXMLElement;

class AuthorMapperTest extends TestCase
{
    private $isiteObject;

    public function setUp()
    {
        $type = 'author.xml';
        $basePath = __DIR__ . '/../../../mock_data/';

        $xml = file_get_contents($basePath . $type);
        $this->isiteObject = new SimpleXMLElement($xml);
    }

    public function testGetsDomainModel()
    {
        $mapperFactory = new MapperFactory($this->createMock(LoggerInterface::class));

        $authorMapper = $mapperFactory->createAuthorsMapper();
        $domainModel = $authorMapper->getDomainModel($this->isiteObject);

        $this->assertEquals(
            "Mark Damazer",
            $domainModel->getName()
        );
    }
}
