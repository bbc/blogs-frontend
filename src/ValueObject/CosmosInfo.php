<?php
declare(strict_types = 1);

namespace App\ValueObject;

class CosmosInfo
{
    /** @var string */
    private $appVersion;

    /** @var string */
    private $appEnvironment;

    /** @var string */
    private $appHost;

    public function __construct(string $appVersion, string $appEnvironment, string $appHost)
    {
        $this->appVersion = $appVersion;
        $this->appEnvironment = $appEnvironment;
        $this->appHost = $appHost;
    }

    public function getAppVersion(): string
    {
        return $this->appVersion;
    }

    public function getAppEnvironment(): string
    {
        return $this->appEnvironment;
    }

    public function getEndpointHost(): string
    {
        return $this->appHost;
    }
}
