<?php
declare(strict_types = 1);
namespace Tests\App\BlogsService\Mapper\IsiteToDomain;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Infrastructure\MapperFactory;
use App\BlogsService\Mapper\IsiteToDomain\BlogMapper;
use PHPUnit\Framework\TestCase;
use SimpleXMLElement;

class BlogMapperTest extends TestCase
{
    private $isiteObject;

    public function setUp()
    {
        $type = 'blogsmetadata.xml';
        $basePath = __DIR__ . '/../../../mock_data/';

        $xml = file_get_contents($basePath . $type);
        $this->isiteObject = new SimpleXMLElement($xml);
    }

    public function testGetsDomainModel()
    {
        $mapperFactory = $this->createMock(MapperFactory::class);
        $blogMapper = new BlogMapper($mapperFactory);

        /** @var Blog $domainModel */
        $domainModel = $blogMapper->getDomainModel($this->isiteObject);

        $this->assertEquals('blogs-aboutthebbc', $domainModel->getProjectId());
        $this->assertEquals('aboutthebbc', $domainModel->getId());
        $this->assertEquals('About the BBC', $domainModel->getName());

        $this->assertEquals(
            'Shining a light on the wealth of BBC activities across the organisation.',
            $domainModel->getShortSynopsis()
        );

        $this->assertEquals(
            'This blog explains what the BBC does and how it works. We link to some other blogs and online spaces inside and outside the corporation. The blog is edited by Jon Jacob.',
            $domainModel->getDescription()
        );
    }
}
