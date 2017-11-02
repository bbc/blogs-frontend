<?php
declare(strict_types = 1);

namespace App\Twig;

use App\Translate\TranslatableTrait;
use App\Translate\TranslateProvider;
use Twig_Extension;
use Twig_Function;

/**
 * The local time functions make use of Translate fairly heavily.
 * Hence grouping local time and translation together.
 */
class TranslateAndTimeExtension extends Twig_Extension
{
    use TranslatableTrait;

    public function __construct(TranslateProvider $translateProvider)
    {
        $this->translateProvider = $translateProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new Twig_Function('tr', [$this, 'trWrapper']),
        ];
    }

    public function trWrapper(
        string $key,
        $substitutions = [],
        $numPlurals = null,
        ?string $domain = null
    ): string
    {
        return $this->tr($key, $substitutions, $numPlurals, $domain);
    }
}