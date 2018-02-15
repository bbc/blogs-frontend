<?php
declare(strict_types = 1);

namespace App\BlogsService\Infrastructure;

use App\BlogsService\Mapper\IsiteToDomain\AuthorMapper;
use App\BlogsService\Mapper\IsiteToDomain\BlogMapper;
use App\BlogsService\Mapper\IsiteToDomain\ContentBlockMapper;
use App\BlogsService\Mapper\IsiteToDomain\Mapper;
use App\BlogsService\Mapper\IsiteToDomain\ModuleMapper;
use App\BlogsService\Mapper\IsiteToDomain\PostMapper;
use App\BlogsService\Mapper\IsiteToDomain\TagMapper;
use Psr\Log\LoggerInterface;

class MapperFactory
{
    protected $instances = [];

    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function createPostMapper(): PostMapper
    {
        return $this->findMapper(PostMapper::class);
    }

    public function createBlogsmetadataMapper(): BlogMapper
    {
        return $this->findMapper(BlogMapper::class);
    }

    public function createModuleMapper(): ModuleMapper
    {
        return $this->findMapper(ModuleMapper::class);
    }

    public function createAuthorsMapper(): AuthorMapper
    {
        return $this->findMapper(AuthorMapper::class);
    }

    public function createTagMapper(): TagMapper
    {
        return $this->findMapper(TagMapper::class);
    }

    public function createContentBlockMapper(): ContentBlockMapper
    {
        return $this->findMapper(ContentBlockMapper::class);
    }

    private function findMapper(string $mapperType): Mapper
    {
        if (!isset($this->instances[$mapperType])) {
            $this->instances[$mapperType] = new $mapperType($this, $this->logger);
        }
        return $this->instances[$mapperType];
    }
}
