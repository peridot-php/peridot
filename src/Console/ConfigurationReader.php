<?php
namespace Peridot\Console;

use Peridot\Configuration;
use ReflectionObject;
use Symfony\Component\Console\Input\InputInterface;

/**
 * The ConfigurationReader is responsible for building a Configuration
 * object from an InputInterface.
 *
 * @package Peridot\Console
 */
class ConfigurationReader
{
    /**
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    protected $input;

    /**
     * @param InputInterface $input
     */
    public function __construct(InputInterface $input)
    {
        $this->input = $input;
    }

    /**
     * Read configuration information from input
     *
     * @return Configuration
     */
    public function read()
    {
        $configuration = new Configuration();
        $configuration->setPaths($this->readPaths());

        $options = [
            'focus' => [$configuration, 'setFocusPattern'],
            'skip' => [$configuration, 'setSkipPattern'],
            'grep' => [$configuration, 'setGrep'],
            'no-colors' => [$configuration, 'disableColors'],
            'force-colors' => [$configuration, 'enableColorsExplicit'],
            'bail' => [$configuration, 'stopOnFailure'],
            'configuration' => [$configuration, 'setConfigurationFile']
        ];

        foreach ($options as $option => $callable) {
            $this->callForOption($option, $callable);
        }

        return $configuration;
    }

    /**
     * Static access to reader
     *
     * @param  InputInterface $input
     * @return Configuration
     */
    public static function readInput(InputInterface $input)
    {
        $reader = new static($input);

        return $reader->read();
    }

    /**
     * Execute a callback if the input object has a value for the
     * given option name.
     *
     * @param string $optionName
     * @param callable $callable
     */
    protected function callForOption($optionName, callable $callable)
    {
        $value = $this->input->getOption($optionName);
        if ($value) {
            call_user_func_array($callable, [$value]);
        }
    }

    private function readPaths()
    {
        // these filthy h4x allow us to determine whether paths were actually
        // passed, or come from the argument's default
        $reflector = new ReflectionObject($this->input);
        $argumentsProperty = $reflector->getProperty('arguments');
        $argumentsProperty->setAccessible(true);
        $arguments = $argumentsProperty->getValue($this->input);
        $paths = $this->input->getArgument('path');

        if (!isset($arguments['path'])) {
            $paths = array_filter($paths, 'file_exists');
        }

        if (empty($paths)) {
            $paths = [getcwd()];
        }

        return $paths;
    }
}
