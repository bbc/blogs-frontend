<?php
declare(strict_types = 1);

namespace App\ValueObject;

use App\BlogsService\Domain\Blog;

class AtiAnalyticsLabels
{
    /** @var string */
    private $appEnvironment;

    /** @var string */
    private $chapterOne;

    /** @var Blog|null */
    private $blog;

    /** @var array */
    private $extraLabels;

    public function __construct(CosmosInfo $cosmosInfo, string $chapterOne, array $extraLabels, ?Blog $blog = null)
    {
        $this->appEnvironment = $cosmosInfo->getAppEnvironment();
        $this->chapterOne = $chapterOne;
        $this->blog = $blog;
        $this->extraLabels = $extraLabels;
    }

    public function setAppEnvironment(string $appEnvironment): void
    {
        $this->appEnvironment = $appEnvironment;
    }

    public function orbLabels(): array 
    {
        $this->blog ? $blogTitle = $this->getBlogTitle() : $blogTitle = null;
        $this->blog ? $hasComments = $this->hasComments() : $hasComments = false;

        $labels = [
            'destination' => $this->getDestination(),
            'section' => $this->chapterOne,
            'additionalProperties' => [
                ['name' => 'app_name', 'value' => 'blogs'],
                ['name' => 'custom_var_1', 'value' => $blogTitle],
                ['name' => 'custom_var_2', 'value' => $hasComments],
            ],
        ];

        return $labels;
    }

    private function getDestination(): string
    {
        $destination =  'blogs_ps';

        if (in_array($this->appEnvironment, ['int', 'stage', 'sandbox', 'test'])) {
            $destination .= '_test';
        }

        return $destination;
    }

    private function getBlogTitle(): string
    {
        return $this->blog->getName();
    }

    private function hasComments(): bool
    {
        return $this->blog->hasCommentsEnabled();
    }
}
