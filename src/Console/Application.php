<?php
namespace Peridot\Console;

use Evenement\EventEmitterInterface;
use Peridot\Configuration;
use Peridot\Reporter\ReporterFactory;
use Peridot\Runner\Context;
use Peridot\Runner\Runner;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Application
 * @package Peridot\Console
 */
class Application extends ConsoleApplication
{
    /**
     * @var \Evenement\EventEmitterInterface
     */
    protected $eventEmitter;

    /**
     * Constructor
     *
     * @param EventEmitterInterface $eventEmitter
     */
    public function __construct(EventEmitterInterface $eventEmitter)
    {
        parent::__construct(Version::NAME, Version::NUMBER);
        $this->eventEmitter = $eventEmitter;
        require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Dsl.php';
    }

    /**
     * Run the peridot application
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $configuration = ConfigurationReader::readInput($input);
        $runner = new Runner(Context::getInstance()->getCurrentSuite(), $configuration, $this->eventEmitter);
        $factory = new ReporterFactory($configuration, $runner, $output, $this->eventEmitter);

        if (file_exists($configuration->getConfigurationFile())) {
            $this->loadConfiguration($this->eventEmitter, $configuration, $factory);
        }

        $this->add(new Command($runner, $configuration, $factory, $this->eventEmitter));

        return parent::doRun($input, $output);
    }

    /**
     * Return the peridot input definition
     *
     * @return InputDefinition|\Symfony\Component\Console\Input\InputDefinition
     */
    protected function getDefaultInputDefinition()
    {
        return new InputDefinition();
    }

    /**
     * @param  InputInterface $input
     * @return string
     */
    public function getCommandName(InputInterface $input)
    {
        return 'peridot';
    }

    /**
     * Load configuration file. If the configuration file returns
     * a callable, it will be executed with the runner, configuration, and reporter factory
     *
     * @param EventEmitterInterface $emitter
     * @param Configuration         $configuration
     * @param ReporterFactory       $reporters
     */
    protected function loadConfiguration(EventEmitterInterface $emitter, Configuration $configuration, ReporterFactory $reporters)
    {
        $func = include $configuration->getConfigurationFile();
        if (is_callable($func)) {
            $func($emitter, $configuration, $reporters);
        }
    }
}
