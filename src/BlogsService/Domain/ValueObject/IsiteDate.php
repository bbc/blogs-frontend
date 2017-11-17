<?php
declare(strict_types=1);

namespace App\BlogsService\Domain\ValueObject;

use DateTimeImmutable;

class IsiteDate
{
    const ISO8601_ISITE = "Y-m-d\TH:i:s.BP";

    /** @var DateTimeImmutable */
    private $date;


    public function __construct(DateTimeImmutable $date)
    {
        $this->date = $date;
    }

    public function __toString()
    {
        return $this->date->format(self::ISO8601_ISITE);
    }
}
