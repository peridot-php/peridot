<?php
namespace Peridot\Reporter;

use Peridot\Configuration;
use Peridot\Runner\Runner;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AnonymousReporter
 * @package Peridot\Reporter
 */
class AnonymousReporter extends AbstractBaseReporter
{
    /**
     * @var callable
     */
    protected $initFn;

    /**
     * Creates a reporter out of a callable
     *
     * @param callable $init
     * @param Configuration $configuration
     * @param Runner $runner
     * @param OutputInterface $output
     */
    public function __construct(callable $init, Configuration $configuration, Runner $runner, OutputInterface $output)
    {
        $this->initFn = $init;
        parent::__construct($configuration, $runner, $output);
    }

    /**
     * Initialize reporter. Setup and listen for runner events
     *
     * @return void
     */
    public function init()
    {
        call_user_func($this->initFn, $this);
    }
}
