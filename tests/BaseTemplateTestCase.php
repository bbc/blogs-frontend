<?php
declare(strict_types = 1);

namespace Tests\App;

use App\Ds\Presenter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;
use Twig_Environment;

abstract class BaseTemplateTestCase extends TestCase
{
    /** @var Twig_Environment */
    static private $twig;

    public static function setUpBeforeClass()
    {
        if (self::$twig === null) {
            self::$twig = TwigEnvironmentProvider::twig();
        }
    }

    /**
     * Get a Dom Crawler populated with a given Twig template.
     *
     * @param string $name The template name
     * @param array $context An array of parameters to pass to the template
     *
     * @return Crawler The Dom Crawler populated with the twig template
     *
     * @throws Twig_Error_Loader  When the template cannot be found
     * @throws Twig_Error_Syntax  When an error occurred during compilation
     * @throws Twig_Error_Runtime When an error occurred during rendering
     */
    protected function crawler(string $name, array $context = []): Crawler
    {
        return new Crawler(
            self::$twig->loadTemplate($name)->render($context)
        );
    }

    protected function presenterCrawler(Presenter $presenter): Crawler
    {
        return $this->crawler($presenter->getTemplatePath(), [
            $presenter->getTemplateVariableName() => $presenter,
        ]);
    }

    protected function assertHasClasses(string $expectedClasses, Crawler $node, $message): void
    {
        $expectedClassesArray = explode(' ', $expectedClasses);
        $classesArray = explode(' ', $node->attr('class'));
        // Check that all classes in $classes are present in
        // the class attribute of the node. Extra classes are ok.
        $hasClasses = !array_diff($expectedClassesArray, $classesArray);

        $this->assertTrue($hasClasses, $message);
    }

    protected function assertSchemaOrgItem(string $expected, Crawler $node)
    {
        $this->assertEquals('http://schema.org/', $node->attr('vocab'));
        $this->assertSchemaOrgTypeOf($expected, $node);
    }

    protected function assertSchemaOrgTypeOf(string $expected, Crawler $node)
    {
        $this->assertEquals($expected, $node->attr('typeof'));
    }

    protected function assertSchemaOrgPropertyAttr(string $expected, Crawler $node, string $property)
    {
        $this->assertEquals(
            $expected,
            $node->filter('[property="' . $property . '"]')->attr('content')
        );
    }
}
