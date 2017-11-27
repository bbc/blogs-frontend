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

class MapperFactory
{
    protected $instances = [];

    public function createPostMapper(): Mapper
    {
        return $this->findMapper(PostMapper::class);
    }

    public function createBlogsmetadataMapper(): Mapper
    {
        return $this->findMapper(BlogMapper::class);
    }

    public function createModuleMapper(): Mapper
    {
        return $this->findMapper(ModuleMapper::class);
    }

    public function createAuthorsMapper(): Mapper
    {
        return $this->findMapper(AuthorMapper::class);
    }

    public function createTagMapper(): Mapper
    {
        return $this->findMapper(TagMapper::class);
    }

    public function createContentBlockMapper(): Mapper
    {
        return $this->findMapper(ContentBlockMapper::class);
    }

    private function findMapper(string $mapperType): Mapper
    {
        if (!isset($this->instances[$mapperType])) {
            $this->instances[$mapperType] = new $mapperType($this);
        }
        return $this->instances[$mapperType];
    }
}
