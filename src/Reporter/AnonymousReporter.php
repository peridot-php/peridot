<?php
namespace Peridot\Reporter;

use Evenement\EventEmitterInterface;
use Peridot\Configuration;
use Peridot\Runner\Runner;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The AnonymousReporter creates a reporter from a PHP callable.
 *
 * @package Peridot\Reporter
 */
class AnonymousReporter extends AbstractBaseReporter
{
    /**
     * @var callable
     */
    protected $initFn;

    /**
     * Creates a reporter from a callable
     *
     * @param callable $init
     * @param Configuration $configuration
     * @param Runner $runner
     * @param OutputInterface $output
     * @param EventEmitterInterface $eventEmitter
     */
    public function __construct(
        callable $init,
        Configuration $configuration,
        Runner $runner,
        OutputInterface $output,
        EventEmitterInterface $eventEmitter
    )
    {
        $this->initFn = $init;
        parent::__construct($configuration, $runner, $output, $eventEmitter);
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function init()
    {
        call_user_func($this->initFn, $this);
    }
}
