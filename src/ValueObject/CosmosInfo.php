<?php
declare(strict_types = 1);

namespace App\ValueObject;

class CosmosInfo
{
    private $appVersion;
    private $appEnvironment;

    public function __construct(string $appVersion, string $appEnvironment)
    {
        $this->appVersion = $appVersion;
        $this->appEnvironment = $appEnvironment;
    }

    public function getAppVersion(): string
    {
        return $this->appVersion;
    }

    public function getAppEnvironment(): string
    {
        return $this->appEnvironment;
    }
}
