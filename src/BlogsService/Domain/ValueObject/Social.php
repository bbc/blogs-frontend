<?php
declare(strict_types = 1);
namespace App\BlogsService\Domain\ValueObject;

class Social
{
    /** @var string */
    private $twitterUsername;

    /** @var string */
    private $facebookUrl;

    /** @var string */
    private $googlePlusUrl;

    public function __construct(
        string $twitterUsername,
        string $facebookUrl,
        string $googlePlusUrl
    ) {
        $this->twitterUsername  = $twitterUsername;
        $this->facebookUrl      = $facebookUrl;
        $this->googlePlusUrl    = $googlePlusUrl;
    }

    public function getTwitterUsername(): string
    {
        return $this->twitterUsername;
    }

    public function getFacebookUrl(): string
    {
        return $this->facebookUrl;
    }

    public function getGooglePlusUrl(): string
    {
        return $this->googlePlusUrl;
    }
}
