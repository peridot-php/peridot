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
     * @param  InputInterface $input
     * @return Configuration
     */
    public function read()
    {
        $configuration = new Configuration();

        if ($path = $this->input->getArgument('path')) {
            $configuration->setPath($path);
        }

        if ($grep = $this->input->getOption('grep')) {
            $configuration->setGrep($grep);
        }

        if ($noColors = $this->input->getOption('no-colors')) {
            $configuration->disableColors();
        }

        if ($bail = $this->input->getOption('bail')) {
            $configuration->stopOnFailure();
        }

        if ($config = $this->input->getOption('configuration')) {
            $configuration->setConfigurationFile($config);
            if (! file_exists($configuration->getConfigurationFile())) {
                throw new \RuntimeException("Configuration file specified but does not exist");
            }
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
}
