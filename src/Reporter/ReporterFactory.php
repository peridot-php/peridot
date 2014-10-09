<?php
namespace Peridot\Reporter;

use Evenement\EventEmitterInterface;
use Peridot\Configuration;
use Peridot\Core\HasEventEmitterTrait;
use Peridot\Runner\Runner;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ReporterFactory
 * @package Peridot\Reporter
 */
class ReporterFactory
{
    use HasEventEmitterTrait;

    /**
     * @var \Peridot\Configuration
     */
    protected $configuration;

    /**
     * @var \Peridot\Runner\Runner
     */
    protected $runner;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * Registered reporters
     *
     * @var array
     */
    protected $reporters = array(
        'spec' => ['description' => 'hierarchical spec list', 'factory' => 'Peridot\Reporter\SpecReporter']
    );

    /**
     * @param Configuration   $configuration
     * @param Runner          $runner
     * @param OutputInterface $output
     */
    public function __construct(
        Configuration $configuration,
        Runner $runner,
        OutputInterface $output,
        EventEmitterInterface $eventEmitter
    )
    {
        $this->configuration = $configuration;
        $this->runner = $runner;
        $this->output = $output;
        $this->eventEmitter = $eventEmitter;
    }

    /**
     * Return an instance of the named reporter
     *
     * @param $name
     * @return \Peridot\Reporter\AbstractBaseReporter
     */
    public function create($name)
    {
        $reporter = isset($this->reporters[$name]) ? $this->reporters[$name] : null;
        $factory = isset($reporter['factory']) ? $reporter['factory'] : null;
        $instance = null;
        if (is_string($factory) && class_exists($factory)) {
            $instance = new $factory($this->configuration, $this->runner, $this->output, $this->eventEmitter);
        }
        if (is_callable($factory)) {
            $instance = new AnonymousReporter($factory, $this->configuration, $this->runner, $this->output, $this->eventEmitter);
        }
        if (is_null($instance)) {
            throw new \RuntimeException("Reporter class could not be created");
        }

        return $instance;
    }

    /**
     * Register a named reporter with the factory
     *
     * @param string $name
     * @param string $description
     * @param string $factory
     */
    public function register($name, $description, $factory)
    {
        $this->reporters[$name] = ['description' => $description, 'factory' => $factory];
    }

    /**
     * @return array
     */
    public function getReporters()
    {
        return $this->reporters;
    }
}
