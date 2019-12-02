<?php
declare(strict_types = 1);

namespace App\Twig;

use App\Translate\TranslatableTrait;
use DateTimeInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig_Function;
use Twig_SimpleFilter;

/**
 * The local time functions make use of Translate fairly heavily.
 * Hence grouping local time and translation together.
 */
class TranslateAndTimeExtension extends AbstractExtension
{
    use TranslatableTrait;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /** @return Twig_SimpleFilter[] */
    public function getFilters(): array
    {
        return [
            new Twig_SimpleFilter('local_date_intl', [$this, 'localDateIntlWrapper']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new Twig_Function('tr', [$this, 'trWrapper']),
        ];
    }

    public function localDateIntlWrapper(DateTimeInterface $dateTime, string $format): string
    {
        return $this->localDateIntl($dateTime, $format);
    }

    public function trWrapper(
        string $key,
        $substitutions = [],
        $numPlurals = null,
        ?string $domain = null
    ): string {
        return $this->tr($key, $substitutions, $numPlurals, $domain);
    }
}
