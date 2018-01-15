<?php
declare(strict_types = 1);

namespace App\ExternalApi\Exception;

use GuzzleHttp\Exception\RequestException;

class CircuitBreakerClosedException extends RequestException
{

}
