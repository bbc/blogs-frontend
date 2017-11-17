<?php
declare(strict_types = 1);
namespace App\Twig;

use App\Ds\Presenter;
use App\Ds\PresenterFactory;
use Twig_Environment;
use Twig_Extension;
use Twig_Function;

class DesignSystemPresenterExtension extends Twig_Extension
{
    /** @var PresenterFactory */
    private $presenterFactory;

    public function __construct(
        PresenterFactory $presenterFactory
    ) {
        $this->presenterFactory = $presenterFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new Twig_Function('ds', [$this, 'ds'], [
                'is_safe' => ['html'],
                'is_variadic' => true,
                'needs_environment' => true,
            ]),
            new Twig_Function('ds_presenter', [$this, 'dsPresenter'], [
                'is_safe' => ['html'],
                'needs_environment' => true,
            ]),
        ];
    }

    public function ds(
        Twig_Environment $twigEnv,
        string $presenterName,
        array $presenterArguments = []
    ): string {
        $presenter = $this->presenterFactory->{$presenterName . 'Presenter'}(...$presenterArguments);

        return $this->dsPresenter($twigEnv, $presenter);
    }

    public function dsPresenter(
        Twig_Environment $twigEnv,
        Presenter $presenter
    ): string {
        return $twigEnv->render(
            $presenter->getTemplatePath(),
            [$presenter->getTemplateVariableName() => $presenter]
        );
    }
}
