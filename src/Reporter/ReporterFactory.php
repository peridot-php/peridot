<?php
namespace Peridot\Reporter;

use Peridot\Runner\Runner;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ReporterFactory
 * @package Peridot\Reporter
 */
class ReporterFactory
{
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
        'basic' => ['description' => 'a simple summary', 'factory' => 'Peridot\Reporter\BasicReporter'],
        'spec' => ['description' => 'hierarchical spec list', 'factory' => 'Peridot\Reporter\SpecReporter']
    );

    /**
     * @param Runner $runner
     * @param OutputInterface $output
     */
    public function __construct(Runner $runner, OutputInterface $output)
    {
        $this->runner = $runner;
        $this->output = $output;
    }

    /**
     * Return an instance of the named reporter
     *
     * @param $name
     * @return \Peridot\Reporter\AbstractBaseReporter
     */
    public function create($name)
    {
        $reporter = $this->reporters[$name];
        $factory = $reporter['factory'];
        $instance = null;
        if (class_exists($factory)) {
            $instance = new $factory($this->runner, $this->output);
        }
        if (is_callable($factory)) {
            $instance = new AnonymousReporter($factory, $this->runner, $this->output);
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
