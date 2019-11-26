<?php
declare(strict_types = 1);

namespace App\Translate;

use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Contracts\Translation\TranslatorTrait;

/**
 * This class exists as we treat English as having 3 pluralisation
 * offsets, whereas Symfony treats it as having 2.
 */
class Translator implements TranslatorInterface
{
    use TranslatorTrait;

    /**
     * This function takes the number of items and returns the
     * pluralisation offset based on said number. We treat all
     * locales as having 3 offsets: none, singular and plural.
     */
    private function getPluralizationRule(int $number, string $locale): int
    {
        return $number === 0 || $number === 1 ? $number : 2;
    }
}
