<?php
declare(strict_types = 1);
namespace Tests\App\BlogsService\Mapper\IsiteToDomain;

use App\BlogsService\Domain\Post;
use App\BlogsService\Infrastructure\MapperFactory;
use App\BlogsService\Mapper\IsiteToDomain\AuthorMapper;
use App\BlogsService\Mapper\IsiteToDomain\ContentBlockMapper;
use App\BlogsService\Mapper\IsiteToDomain\PostMapper;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
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
        $postMapper = new PostMapper($mapperFactory, $this->createMock(LoggerInterface::class));

        /** @var Post $domainModel */
        $domainModel = $postMapper->getDomainModel($isiteObject);

        $this->assertInstanceOf(Post::class, $domainModel);

        $this->assertEquals(
            "Proposed changes to the BBC Pension Scheme",
            $domainModel->getTitle()
        );
    }

    /**
     * @dataProvider invalidContentBlockProvider
     */
    public function testIgnoresInvalidContentBlocks(string $type)
    {
        $basePath = __DIR__ . '/../../../mock_data/';

        $xml = file_get_contents($basePath . $type);
        $isiteObject = new SimpleXMLElement($xml);

        $mapperFactory = $this->createMock(MapperFactory::class);

        $contentBlockMapper = new ContentBlockMapper($mapperFactory, $this->createMock(LoggerInterface::class));
        $authorMapper = new AuthorMapper($mapperFactory, $this->createMock(LoggerInterface::class));

        $mapperFactory->method('createContentBlockMapper')->willReturn($contentBlockMapper);
        $mapperFactory->method('createAuthorsMapper')->willReturn($authorMapper);

        $postMapper = new PostMapper($mapperFactory, $this->createMock(LoggerInterface::class));

        /** @var Post $domainModel */
        $domainModel = $postMapper->getDomainModel($isiteObject);

        $this->assertInstanceOf(Post::class, $domainModel);

        $this->assertNotContains(null, $domainModel->getContent());
    }

    public function invalidContentBlockProvider(): array
    {
        return [
            'generallyInvalid' => ['type' => 'post_invalidcontentblocks.xml'],
            'invalidSmps' => ['type' => 'post_invalidsmp.xml'],
        ];
    }
}
