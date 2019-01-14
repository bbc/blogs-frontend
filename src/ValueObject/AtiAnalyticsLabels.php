<?php
declare(strict_types = 1);

namespace App\ValueObject;



class AtiAnalyticsLabels
{
    /** @param string */
    private $appEvironment;

    public function setAppEnvironment(string $appEnvironment): void
    {
        $this->appEnvironment = $appEnvironment;
    }

    public function __construct( CosmosInfo $cosmosInfo)
    {
        $this->appEnvironment = $cosmosInfo->getAppEnvironment();
    }

    public function orbLabels()
    {
        $labels = [
            //'destination' => $this->getDestination(),
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
