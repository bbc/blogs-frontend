<?php
declare(strict_types = 1);
namespace Tests\App\BlogsService\Mapper\IsiteToDomain;

use App\BlogsService\Domain\Post;
use App\BlogsService\Infrastructure\MapperFactory;
use App\BlogsService\Mapper\IsiteToDomain\AuthorMapper;
use App\BlogsService\Mapper\IsiteToDomain\ContentBlockMapper;
use App\BlogsService\Mapper\IsiteToDomain\PostMapper;
use PHPUnit\Framework\TestCase;
use SimpleXMLElement;

class PostMapperTest extends TestCase
{
    public function testGetsDomainModel()
    {
        $type = 'post.xml';
        $basePath = __DIR__ . '/../../../mock_data/';

        $xml = file_get_contents($basePath . $type);
        $isiteObject = new SimpleXMLElement($xml);

        $mapperFactory = $this->createMock(MapperFactory::class);
        $postMapper = new PostMapper($mapperFactory);

        /** @var Post $domainModel */
        $domainModel = $postMapper->getDomainModel($isiteObject);

        $this->assertInstanceOf(Post::class, $domainModel);

        $this->assertEquals(
            "Proposed changes to the BBC Pension Scheme",
            $domainModel->getTitle()
        );
    }

    public function testIgnoresInvalidContentBlocks()
    {
        $type = 'post_invalidcontentblocks.xml';
        $basePath = __DIR__ . '/../../../mock_data/';

        $xml = file_get_contents($basePath . $type);
        $isiteObject = new SimpleXMLElement($xml);

        $mapperFactory = $this->createMock(MapperFactory::class);

        $contentBlockMapper = new ContentBlockMapper($mapperFactory);
        $authorMapper = new AuthorMapper($mapperFactory);

        $mapperFactory->method('createContentBlockMapper')->willReturn($contentBlockMapper);
        $mapperFactory->method('createAuthorsMapper')->willReturn($authorMapper);

        $postMapper = new PostMapper($mapperFactory);

        /** @var Post $domainModel */
        $domainModel = $postMapper->getDomainModel($isiteObject);

        $this->assertInstanceOf(Post::class, $domainModel);

        $this->assertNotContains(null, $domainModel->getContent());
    }
}
