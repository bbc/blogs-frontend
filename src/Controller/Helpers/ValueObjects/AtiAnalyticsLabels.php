<?php
declare(strict_types = 1);

namespace App\Controller\Helpers\ValueObjects;

class AtiAnalyticsLabels
{
    /** @var array */
    private $labels;

    public function __construct(array $labels)
    {
        $this->labels = $labels;
    }

    public function getLabels(): array
    {
        return $this->labels;
    }
}
