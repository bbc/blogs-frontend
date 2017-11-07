<?php
declare(strict_types = 1);
namespace App\BlogsService\Domain\ValueObject;

use InvalidArgumentException;

class GUID
{
    /** @var string */
    private $guid;

    public function __construct(string $guid)
    {
        if (!$this->isValidGUID($guid)) {
            throw new InvalidArgumentException('The GUID supplied (' . $guid . ') is invalid');
        }

        $this->guid = $guid;
    }

    public function getGuid(): string
    {
        return $this->guid;
    }

    public function __toString(): string
    {
        return $this->getGuid();
    }

    private function isValidGUID(string $guid): bool
    {
        /**
         * Logic Breakdown:
         *
         * Stackoverflow url : http://stackoverflow.com/questions/1253373/php-check-for-valid-guid
         *
         * / beginning of expression
         * ^ beginning of string
         * (\{)? optional opening bracket {
         * [a-f\d]{8} 8 hex characters hhhhhhhh
         * (-[a-f\d]{4}) 4 hex characters proceeded by dash -hhhh
         * {4} previous pattern repeated 4 times
         * [a-f\d]{8} 8 hex characters hhhhhhhh
         * (?(1)\}) if first pattern was present {, then match closing tag }
         * $ end of string
         * / close expression
         * i ignore case sensitivity
         */
        return !!preg_match("/^(\{)?[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}(?(1)\})$/i", $guid);
    }
}
