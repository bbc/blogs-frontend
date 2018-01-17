<?php
declare(strict_types = 1);

namespace Tests\App\BlogsService\Service;

use BBC\ProgrammesCachingLibrary\Cache;
use App\BlogsService\Infrastructure\MapperFactory;
use App\BlogsService\Infrastructure\XmlParser;
use App\BlogsService\Service\ServiceFactory;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\TestCase;

class ServiceFactoryTest extends TestCase
{
    const SERVICE_NS = 'App\BlogsService\Service\\';

    /** @dataProvider serviceNameDataProvider */
    public function testGetters(string $serviceName, string $expectedMapper)
    {
        // Ensure we're calling the correct mapper
        $mockMapperFactory = $this->createMock(MapperFactory::class);
        $mockMapperFactory->expects($this->exactly(1))->method($expectedMapper);

        $serviceFactory = new ServiceFactory(
            'imAnApiEnd.Point',
            $this->createMock(ClientInterface::class),
            $mockMapperFactory,
            $this->createMock(Cache::class),
            $this->createMock(XmlParser::class)
        );

        $service = $serviceFactory->{'get' . $serviceName}();

        $this->assertInstanceOf(self::SERVICE_NS . $serviceName, $service);

        // Requesting the same service multiple times reuses the same instance
        $this->assertSame($service, $serviceFactory->{'get' . $serviceName}());
    }

    public function serviceNameDataProvider()
    {
        return [
            'BlogService' => ['BlogService', 'createBlogsmetadataMapper'],
            'PostService' => ['PostService', 'createPostMapper'],
            'TagService' => ['TagService', 'createTagMapper'],
        ];
    }
}
