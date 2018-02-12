<?php
declare(strict_types = 1);
namespace Tests\App\BlogsService\Mapper\IsiteToDomain;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Infrastructure\MapperFactory;
use App\BlogsService\Mapper\IsiteToDomain\BlogMapper;
use App\BlogsService\Mapper\IsiteToDomain\PostMapper;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use SimpleXMLElement;

class BlogMapperTest extends TestCase
{
    public function testGetsDomainModel()
    {
        $type = 'blogsmetadata.xml';
        $basePath = __DIR__ . '/../../../mock_data/';

        $xml = file_get_contents($basePath . $type);
        $isiteObject = new SimpleXMLElement($xml);

        $mapperFactory = $this->createMock(MapperFactory::class);
        $blogMapper = new BlogMapper($mapperFactory, $this->createMock(LoggerInterface::class));

        /** @var Blog $domainModel */
        $domainModel = $blogMapper->getDomainModel($isiteObject);

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

    public function testIgnoresInvalidFeaturedPost()
    {
        $type = 'blogsmetadata_invalidfeatured.xml';
        $basePath = __DIR__ . '/../../../mock_data/';

        $xml = file_get_contents($basePath . $type);
        $isiteObject = new SimpleXMLElement($xml);

        $mapperFactory = $this->createMock(MapperFactory::class);
        $postMapper = new PostMapper($mapperFactory, $this->createMock(LoggerInterface::class));
        $mapperFactory->method('createPostMapper')->willReturn($postMapper);
        $blogMapper = new BlogMapper($mapperFactory, $this->createMock(LoggerInterface::class));

        /** @var Blog $domainModel */
        $blog = $blogMapper->getDomainModel($isiteObject);

        $this->assertNull($blog->getFeaturedPost());
    }
}
