<?php
declare(strict_types = 1);

namespace Tests\App\BlogsService\Infrastructure;

use App\BlogsService\Infrastructure\MapperFactory;
use App\BlogsService\Mapper\IsiteToDomain\AuthorMapper;
use App\BlogsService\Mapper\IsiteToDomain\BlogMapper;
use App\BlogsService\Mapper\IsiteToDomain\ModuleMapper;
use App\BlogsService\Mapper\IsiteToDomain\PostMapper;
use App\BlogsService\Mapper\IsiteToDomain\TagMapper;
use PHPUnit\Framework\TestCase;

class MapperFactoryTest extends TestCase
{
    /** @var MapperFactory */
    private $mapperFactory;

    public function setUp()
    {
        $this->mapperFactory = new MapperFactory();
    }

    /** @dataProvider isiteDataProvider */
    public function testCreatesCorrectMapperForIsite($method, $class)
    {
        $methodName = 'create' . $method . 'Mapper';
        $this->assertInstanceOf($class, $this->mapperFactory->{$methodName}());
    }

    public function isiteDataProvider()
    {
        return [
            ['method' => 'Authors', 'class' => AuthorMapper::class],
            ['method' => 'Blogsmetadata', 'class' => BlogMapper::class],
//            ['method' => 'ContentBlock', 'class' => ContentBlockMapper::class],
            ['method' => 'Module', 'class' => ModuleMapper::class],
            ['method' => 'Post', 'class' => PostMapper::class],
            ['method' => 'Tag', 'class' => TagMapper::class],
        ];
    }
}
