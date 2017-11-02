<?php
declare(strict_types = 1);

namespace App\Translate;

trait TranslatableTrait
{
    /** @var TranslateProvider */
    protected $translateProvider;

    protected function tr(
        string $key,
        $substitutions = [],
        $numPlurals = null,
        ?string $domain = null
    ): string
    {
        if (is_int($substitutions) && is_null($numPlurals)) {
            $numPlurals = $substitutions;
            $substitutions = ['%count%' => $numPlurals];
        }
        if (is_int($numPlurals) && !isset($substitutions['%count%'])) {
            $substitutions['%count%'] = $numPlurals;
        }
        return $this->translateProvider->getTranslate()->translate($key, $substitutions, $numPlurals, $domain);
    }
}