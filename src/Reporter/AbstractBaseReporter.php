<?php
namespace Peridot\Reporter;

use Peridot\Core\Spec;
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
     * @var array
     */
    protected $errors = [];

    /**
     * @var int
     */
    protected $passing = 0;

    /**
     * @param Runner $runner
     * @param OutputInterface $output
     */
    public function __construct(Runner $runner, OutputInterface $output)
    {
        $this->runner = $runner;
        $this->output = $output;

        $this->runner->on('fail', function(Spec $spec, \Exception $e) {
            $this->errors[] = $e;
        });

        $this->runner->on('pass', function() {
            $this->passing++;
        });

        $this->init();
    }

    /**
     * Initialize reporter. Setup and listen for runner events
     *
     * @return void
     */
    abstract public function init();
}
