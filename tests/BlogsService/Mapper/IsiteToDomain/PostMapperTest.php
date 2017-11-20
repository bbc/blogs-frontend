<?php
declare(strict_types = 1);
namespace Tests\App\BlogsService\Mapper\IsiteToDomain;

use App\BlogsService\Domain\Post;
use App\BlogsService\Infrastructure\MapperFactory;
use App\BlogsService\Mapper\IsiteToDomain\PostMapper;
use PHPUnit\Framework\TestCase;
use SimpleXMLElement;

class PostMapperTest extends TestCase
{
    private $isiteObject;

    public function setUp()
    {
        $type = 'post.xml';
        $basePath = __DIR__ . '/../../../mock_data/';

        $xml = file_get_contents($basePath . $type);
        $this->isiteObject = new SimpleXMLElement($xml);
    }

    public function testGetsDomainModel()
    {
        $mapperFactory = $this->createMock(MapperFactory::class);
        $postMapper = new PostMapper($mapperFactory);

        /** @var Post $domainModel */
        $domainModel = $postMapper->getDomainModel($this->isiteObject);

        $this->assertInstanceOf(Post::class, $domainModel);

        $this->assertEquals(
            "Proposed changes to the BBC Pension Scheme",
            $domainModel->getTitle()
        );
    }
}
