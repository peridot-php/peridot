<?php
namespace Peridot\Console;

use Evenement\EventEmitterInterface;
use Peridot\Core\HasEventEmitterTrait;
use Peridot\Runner\Context;

/**
 * Environment is responsible for creating necessary objects and conditions
 * for Peridot to run. It creates the event emitter, input definition, and includes
 * user configuration from the Peridot configuration file.
 *
 * @package Peridot\Console
 */
class Environment
{
    use HasEventEmitterTrait;

    /**
     * @var InputDefinition
     */
    protected $definition;

    /**
     * Environment options
     *
     * @var array
     */
    protected $options;

    /**
     * @param InputDefinition $definition
     * @param EventEmitterInterface $emitter
     * @param array $options
     */
    public function __construct(
        InputDefinition $definition,
        EventEmitterInterface $emitter,
        array $options
    ) {
        $this->definition = $definition;
        $this->eventEmitter = $emitter;
        $this->options = $options;
        $this->initializeContext($emitter);
    }

    /**
     * Attempt to load a user configuration file into the Peridot
     * environment
     *
     * @param  string $configuration The default configuration path
     *
     * @return bool
     */
    public function load($configuration)
    {
        return $this->loadConfiguration($configuration);
    }

    /**
     * Return the InputDefinition used to define the available Peridot
     * options and arguments
     *
     * @return \Peridot\Console\InputDefinition
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * Load configuration
     *
     * @param $configuration
     * @return bool
     */
    protected function loadConfiguration($configuration)
    {
        if (! $this->wasGivenAConfigurationPath()) {
            return $this->includeConfiguration($configuration);
        }

        $files = array_filter(['c', 'configuration'], [$this, 'optionIsFile']);

        if (empty($files)) {
            return false;
        }

        return $this->includeConfiguration($this->options[array_pop($files)]);
    }

    /**
     * Determine if the environment option identified by $key
     * is a file.
     *
     * @param $key
     */
    protected function optionIsFile($key)
    {
        if (! array_key_exists($key, $this->options)) {
            return false;
        }

        return is_file($this->options[$key]);
    }

    /**
     * Include the configuration file used to setup the peridot
     * environment
     *
     * @param $configuration
     * @return bool
     */
    protected function includeConfiguration($configuration)
    {
        if (file_exists($configuration)) {
            $callable = include $configuration;
            if (is_callable($callable)) {
                call_user_func($callable, $this->eventEmitter);
            }
        }

        return true;
    }

    /**
     * Returns true if the Environment was given a configuration path.
     *
     * @return bool
     */
    protected function wasGivenAConfigurationPath()
    {
        return isset($this->options['c']) || isset($this->options['configuration']);
    }

    /**
     * Initialize the Context with the same event emitter as the Environment.
     *
     * @param EventEmitterInterface $emitter
     */
    protected function initializeContext(EventEmitterInterface $emitter)
    {
        Context::getInstance()->setEventEmitter($emitter);
    }
}
