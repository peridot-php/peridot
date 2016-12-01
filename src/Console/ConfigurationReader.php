<?php
namespace Peridot\Console;

use Peridot\Configuration;
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

        $options = [
            'focus' => [$configuration, 'setFocusPattern'],
            'skip' => [$configuration, 'setSkipPattern'],
            'grep' => [$configuration, 'setGrep'],
            'no-colors' => [$configuration, 'disableColors'],
            'force-colors' => [$configuration, 'enableColorsExplicit'],
            'bail' => [$configuration, 'stopOnFailure'],
            'configuration' => [$configuration, 'setConfigurationFile']
        ];

        if ($path = $this->input->getArgument('path')) {
            $configuration->setPath($path);
        }

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
}
