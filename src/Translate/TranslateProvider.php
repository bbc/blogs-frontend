<?php
declare(strict_types = 1);

namespace App\Translate;

use RMP\Translate\Translate;
use RMP\Translate\TranslateFactory;

/**
 * Class TranslateProvider
 *
 * This class exists so that we can override the page language at the controller
 * stage. If we directly inject RMP\Translate via the DI container, we can't change the language
 * after it's been passed in to our various classes.
 */
class TranslateProvider
{
    /** @var string */
    private $locale = 'en_GB';

    /** @var Translate|null */
    private $translate;

    /** @var TranslateFactory */
    private $translateFactory;

    public function __construct(TranslateFactory $translateFactory)
    {
        $this->translateFactory = $translateFactory;
    }

    public function setLocale(string $locale): void
    {
        if ($locale !== $this->locale) {
            $this->locale = $locale;
            $this->translate = null;
        }
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getTranslate(): Translate
    {
        if (!$this->translate) {
            $this->translate = $this->translateFactory->create($this->locale);
        }
        return $this->translate;
    }
}