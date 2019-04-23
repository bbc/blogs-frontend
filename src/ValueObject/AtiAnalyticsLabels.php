<?php
declare(strict_types = 1);

namespace App\ValueObject;

class AtiAnalyticsLabels
{
    /** @var string */
    private $appEnvironment;

    /** @var string */
    private $chapterOne;

    public function __construct(CosmosInfo $cosmosInfo, string $chapterOne)
    {
        $this->appEnvironment = $cosmosInfo->getAppEnvironment();
        $this->chapterOne = $chapterOne;
    }

    public function setAppEnvironment(string $appEnvironment): void
    {
        $this->appEnvironment = $appEnvironment;
    }

    public function orbLabels()
    {
        $labels = [
            'destination' => $this->getDestination(),
            'section' => $this->chapterOne,
            'additionalProperties' => [
                ['name' => 'app_name', 'value' => 'blogs'],
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
}
