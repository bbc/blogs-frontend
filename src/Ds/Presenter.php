<?php
declare(strict_types = 1);
namespace App\Ds;

/**
 * Base Class for a all design system Presenters
 */
abstract class Presenter
{
    /** @var array */
    protected static $presenterNameCache = [];

    /** @var array */
    protected static $templatePathCache = [];

    /** @var array */
    protected $options = [];

    /** @var string */
    protected $uniqueId;

    public function __construct(array $options = [])
    {
        $this->options = array_merge($this->options, $options);
        $this->validateOptions($this->options);
    }

    /**
     * Full path to the twig template the presenter class renders
     */
    public function getTemplatePath(): string
    {
        if (!isset(self::$templatePathCache[static::class])) {
            $classPath = str_replace('App\\Ds\\', '', static::class);

            $parts = implode('/', explode('\\', $classPath, -1));
            $pathPrefix = '@Ds/' . $parts . ($parts ? '/' : '');

            self::$templatePathCache[static::class] = $pathPrefix . static::snakeCasePresenterName() . '.html.twig';
        }

        return self::$templatePathCache[static::class];
    }

    /**
     * Get the base property that the twig template will be expecting
     * to find it's variables under. Twig conventions say variable names should
     * use snake_case. So the "myExamplePresenter" presenter's variable name
     * would be "my_example"
     */
    public function getTemplateVariableName(): string
    {
        return static::snakeCasePresenterName();
    }

    public function getOption($keyOption)
    {
        if (array_key_exists($keyOption, $this->options)) {
            return $this->options[$keyOption];
        }

        throw new InvalidOptionException(sprintf(
            'Called getOption with an invalid value. Expected one of %s but got "%s"',
            '"' . implode('", "', array_keys($this->options)) . '"',
            $keyOption
        ));
    }

    /**
     * Get or generate a unique ID. Once generated once the same one will be used
     * Only used for unique references in a single render
     */
    public function getUniqueId(): string
    {
        if (!$this->uniqueId) {
            $parts = explode('\\', static::class);
            $class = end($parts);
            $this->uniqueId = 'ds-' . $class . '-' . mt_rand();
        }

        return $this->uniqueId;
    }

    /**
     * Validate options. Should be overridden.
     */
    protected function validateOptions(array $options): void
    {
    }

    /**
     * Calculates the presenter name for use in referencing the template and
     * the name of the variable to be passed to that template.
     * e.g. App\DsShared\Organism\ExampleThingPresenter becomes example_thing
     *
     */
    protected static function snakeCasePresenterName(): string
    {
        if (!isset(self::$presenterNameCache[static::class])) {
            $namespaceEnd = strrpos(static::class, '\\');
            $shortClassName = substr(static::class, $namespaceEnd + ($namespaceEnd ? 1: 0));

            // Trim the class name from the word 'Presenter'
            $presenterName = substr($shortClassName, 0, strpos($shortClassName, 'Presenter'));

            self::$presenterNameCache[static::class] = strtolower(preg_replace(
                ["/([A-Z]+)/", "/_([A-Z]+)([A-Z][a-z])/"],
                ["_$1", "_$1_$2"],
                lcfirst($presenterName)
            ));
        }
        return self::$presenterNameCache[static::class];
    }
}
