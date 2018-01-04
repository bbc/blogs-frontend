<?php
declare(strict_types = 1);
namespace App\Twig;

use Symfony\Component\Asset\Packages;
use Twig_Extension;
use Twig_Function;

class HtmlUtilitiesExtension extends Twig_Extension
{
    private $packages;

    private $snippets;

    public function __construct(Packages $packages)
    {
        $this->packages = $packages;
        $this->snippets = [];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new Twig_Function('asset_js', [$this, 'assetJs']),
            new Twig_Function('build_css_classes', [$this, 'buildCssClasses']),
            new Twig_Function('add_script_snippet', [$this, 'addScriptSnippet']),
            new Twig_Function('build_script_snippets', [$this, 'buildScriptSnippets']),
        ];
    }

    /**
     * Given a js asset path, return it in a format suitable for inclusion in
     * the require config (i.e. with the ".js" extension removed)
     */
    public function assetJs(string $path): string
    {
        return preg_replace('/\.js$/', '', $this->packages->getUrl('js/' . $path, null));
    }

    public function buildCssClasses(array $cssClassTests = []): string
    {
        $cssClasses = [];
        foreach ($cssClassTests as $cssClass => $shouldSet) {
            if ($shouldSet) {
                $cssClasses[] = $cssClass;
            }
        }
        return trim(implode(' ', $cssClasses));
    }

    public function addScriptSnippet(string $snippet)
    {
        if ($snippet) {
            $this->snippets[] = $snippet;
        }
    }

    public function buildScriptSnippets(): string
    {
        if (empty($this->snippets) && empty($this->smps)) {
            return '';
        }

        $snippetsSource = '<script>';
        foreach ($this->snippets as $snippet) {
            $snippetsSource .= $snippet;
        }
        $snippetsSource .= '</script>';

        return $snippetsSource;
    }
}
