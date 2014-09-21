<?php
namespace Peridot\Reporter;

use Peridot\Runner\Runner;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AbstractBaseReporter
 * @package Peridot\Reporter
 */
abstract class AbstractBaseReporter
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
     * @param Runner $runner
     * @param OutputInterface $output
     */
    public function __construct(Runner $runner, OutputInterface $output)
    {
        $this->runner = $runner;
        $this->output = $output;
        $this->init();
    }

    /**
     * Report results
     *
     * @return void
     */
    abstract public function init();
}
