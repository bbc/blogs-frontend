<?php
declare(strict_types = 1);

namespace App\ValueObject;

use App\BlogsService\Domain\Blog;

class AnalyticsCounterName
{
    /** @var string */
    private $counterName;

    public function __construct(?Blog $blog, string $pageName = '')
    {
        $this->counterName = '';

        if ($blog !== null) {
            $istatsCountername = $blog->getIstatsCountername();
            if ($istatsCountername) {
                $this->counterName = $istatsCountername . '.';
            }
        }

        $this->counterName .= 'blogs' . '.';
        if ($pageName !== '') {
            $this->counterName .= $this->replaceDisallowedCharacters($pageName) . '.';
        }

        $this->counterName .= 'page';
    }

    public function __toString(): string
    {
        return $this->counterName;
    }

    private function replaceDisallowedCharacters(string $string): string
    {
        $string = preg_replace(['/[^a-zA-Z0-9_.]/', '{_+}', '{\._}'], '_', $string);
        $string = str_replace('_.', '.', $string);
        $string = preg_replace('{_+}', '_', $string);
        $string = trim($string, '_');
        $string = strtolower($string);

        return $string;
    }
}
