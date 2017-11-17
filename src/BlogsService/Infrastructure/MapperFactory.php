<?php
declare(strict_types = 1);

namespace App\BlogsService\Infrastructure;

use App\BlogsService\Mapper\IsiteToDomain\AuthorMapper;
use App\BlogsService\Mapper\IsiteToDomain\BlogMapper;
use App\BlogsService\Mapper\IsiteToDomain\ContentBlockMapper;
use App\BlogsService\Mapper\IsiteToDomain\ModuleMapper;
use App\BlogsService\Mapper\IsiteToDomain\PostMapper;
use App\BlogsService\Mapper\IsiteToDomain\TagMapper;

class MapperFactory
{
    protected $instances = [];

    public function createPostMapper(): PostMapper
    {
        if (!isset($this->instances[PostMapper::class])) {
            $this->instances[PostMapper::class] = new PostMapper($this);
        }
        return $this->instances[PostMapper::class];
    }

    public function createBlogsmetadataMapper(): BlogMapper
    {
        if (!isset($this->instances[BlogMapper::class])) {
            $this->instances[BlogMapper::class] = new BlogMapper($this);
        }
        return $this->instances[BlogMapper::class];
    }

    public function createModuleMapper(): ModuleMapper
    {
        if (!isset($this->instances[ModuleMapper::class])) {
            $this->instances[ModuleMapper::class] = new ModuleMapper($this);
        }
        return $this->instances[ModuleMapper::class];
    }

    public function createAuthorsMapper(): AuthorMapper
    {
        if (!isset($this->instances[AuthorMapper::class])) {
            $this->instances[AuthorMapper::class] = new AuthorMapper($this);
        }
        return $this->instances[AuthorMapper::class];
    }

    public function createTagMapper(): TagMapper
    {
        if (!isset($this->instances[TagMapper::class])) {
            $this->instances[TagMapper::class] = new TagMapper($this);
        }
        return $this->instances[TagMapper::class];
    }

    public function createContentBlockMapper(): ContentBlockMapper
    {
        if (!isset($this->instances[ContentBlockMapper::class])) {
            $this->instances[ContentBlockMapper::class] = new ContentBlockMapper($this);
        }
        return $this->instances[ContentBlockMapper::class];
    }
}
