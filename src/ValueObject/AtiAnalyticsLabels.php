<?php
declare(strict_types = 1);

namespace App\ValueObject;

class AtiAnalyticsLabels
{
    /** @var string */
    private $appEnvironment;

    public function __construct(CosmosInfo $cosmosInfo)
    {
        $this->appEnvironment = $cosmosInfo->getAppEnvironment();
    }

    public function setAppEnvironment(string $appEnvironment): void
    {
        $this->appEnvironment = $appEnvironment;
    }

    public function orbLabels()
    {
        //this is commented out because we want it to fallback to the default bbc bucket, until a blogs specific bucket
        //is created in the near future.
        $labels = [
            //'destination' => $this->getDestination(),
            'additionalProperties' => [
                ['name' => 'app_name', 'value' => 'blogs'],
            ],
        ];

        return $labels;
    }

    private function getDestination(): string
    {
        $destination =  'programmes_ps';

        if (in_array($this->appEnvironment, ['int', 'stage', 'sandbox', 'test'])) {
            $destination .= '_test';
        }

        return $destination;
    }
}
