<?php
declare(strict_types = 1);

namespace App\Controller\Helpers\Services;

use App\BlogsService\Domain\Blog;
use App\Controller\Helpers\ValueObjects\AtiAnalyticsLabels;
use App\ValueObject\CosmosInfo;

class AtiAnalyticsHelper
{
    /** @var string */
    private $chapterOne;

    /** @var CosmosInfo */
    private $cosmosInfo;

    public function __construct(CosmosInfo $cosmosInfo)
    {
        $this->cosmosInfo = $cosmosInfo;
    }

    public function setChapterOneVariable(string $chapterOne): void
    {
        $this->chapterOne = $chapterOne;
    }

    public function makeLabels(string $chapterOne, string $contentType, ?Blog $blog = null, bool $hasVideo = false,?string $guid = ""): AtiAnalyticsLabels
    {
        $labels = [
            'destination' => $this->getDestination(),
            'section' => $chapterOne,
            'contentId' => 'urn:bbc:isite' . ($guid ? ":" . $guid : ''),
            'contentType' => $contentType,
            'additionalProperties' => [
                ['name' => 'app_name', 'value' => 'blogs'],
                ['name' => 'custom_var_1', 'value' => $this->getBlogTitle($blog)],
                ['name' => 'custom_var_2', 'value' => $this->hasComments($blog) ? 'true' : 'false'],
                ['name' => 'custom_var_3', 'value' => $hasVideo ? 'true' : 'false'],
            ],
        ];

        return new AtiAnalyticsLabels($labels);
    }

    private function getDestination(): string
    {
        $destination =  'blogs_ps';

        if (in_array($this->cosmosInfo->getAppEnvironment(), ['int', 'stage', 'sandbox', 'test'])) {
            $destination .= '_test';
        }

        return $destination;
    }

    private function getBlogTitle(?Blog $blog): string
    {
        return $blog ? $blog->getName() : '';
    }

    private function hasComments(?Blog $blog): bool
    {
        return $blog ? $blog->hasCommentsEnabled() : false;
    }
}
