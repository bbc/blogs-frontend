<?php
declare(strict_types = 1);

namespace Tests\App;

use App\Ds\PresenterFactory;
use App\Translate\TranslateProvider;
use App\Twig\DesignSystemPresenterExtension;
use App\Twig\GelIconExtension;
use App\Twig\HtmlUtilitiesExtension;
use App\Twig\TranslateAndTimeExtension;
use App\ValueObject\CosmosInfo;
use RMP\Translate\TranslateFactory;
use Symfony\Bridge\Twig\Extension\AssetExtension;
use Symfony\Bridge\Twig\Extension\RoutingExtension;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollectionBuilder;
use Twig_Environment;
use Twig_Loader_Filesystem;

class TwigEnvironmentProvider
{
    /** @var Twig_Environment */
    static private $twig;

    /** @var PresenterFactory */
    static private $dsPresenterFactory;

    public static function twig(): Twig_Environment
    {
        if (self::$twig === null) {
            self::build();
        }
        return self::$twig;
    }

    public static function dsPresenterFactory(): PresenterFactory
    {
        if (self::$dsPresenterFactory === null) {
            self::build();
        }
        return self::$dsPresenterFactory;
    }

    private static function build(): void
    {
        $loader = new Twig_Loader_Filesystem();
        $loader->addPath(__DIR__ . '/../src/Ds', 'Ds');
        $twig = new Twig_Environment($loader, ['strict_variables' => true]);
        $translateFactory = new TranslateFactory([
            'fallback_locale' => 'en_GB',
            'cachepath' => __DIR__ . '/../var/cache/test/translations',
            'domains' => ['blogs'],
            'default_domain' => 'blogs',
            'debug' => true,
            'basepath' => __DIR__ . '/../config/translations',
        ]);
        $translateProvider = new TranslateProvider($translateFactory);
        $assetPackages = new Packages(new Package(new EmptyVersionStrategy()));
        $routeCollectionBuilder = new RouteCollectionBuilder(new YamlFileLoader(
            new FileLocator([__DIR__ . '/../config'])
        ));
        $routeCollectionBuilder->import('routes.yaml');
        // Symfony extensions
        $twig->addExtension(new AssetExtension($assetPackages));
        $router = new UrlGenerator(
            $routeCollectionBuilder->build(),
            new RequestContext()
        );
        $twig->addExtension(new RoutingExtension($router));
        // Set presenter factory for template tests to use.
        self::$dsPresenterFactory = new PresenterFactory(new CosmosInfo('1', 'test', 'http://localhost'));
        $twig->addExtension(new DesignSystemPresenterExtension(self::$dsPresenterFactory));
        $twig->addExtension(new TranslateAndTimeExtension($translateProvider));
        $twig->addExtension(new GelIconExtension());
        $twig->addExtension(new HtmlUtilitiesExtension($assetPackages));
        // Set twig for template tests to use
        self::$twig = $twig;
    }
}
