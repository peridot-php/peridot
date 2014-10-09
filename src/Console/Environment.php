<?php
namespace Peridot\Console;

use Evenement\EventEmitterInterface;
use Peridot\Core\HasEventEmitterTrait;

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
     * Constructor
     *
     * @param array $options
     */
    public function __construct(
        InputDefinition $definition,
        EventEmitterInterface $emitter,
        array $options
    )
    {
        $this->definition = $definition;
        $this->eventEmitter = $emitter;
        $this->options = $options;
    }

    /**
     * @param  string $configuration The default configuration path
     * @return bool
     */
    public function load($configuration)
    {
        return $this->loadConfiguration($configuration);
    }

    /**
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
        if (isset($this->options['c']) || isset($this->options['configuration'])) {
            $keys = ['c', 'configuration'];
            $options = $this->options;
            $file = array_reduce($keys, function ($result, $key) use ($options) {
                return (array_key_exists($key, $options) && is_file($options[$key])) ? $options[$key] : $result;
            }, null);

            if (is_null($file)) {
                return false;
            }

            $configuration = $file;
        }

        return $this->includeConfiguration($configuration);
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
}
