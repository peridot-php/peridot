<?php
namespace Peridot\Runner;

use Evenement\EventEmitterInterface;
use Peridot\Configuration;
use Peridot\Core\HasEventEmitterTrait;
use Peridot\Core\SpecResult;
use Peridot\Core\Suite;

/**
 * The Runner is responsible for running a given Suite.
 *
 * @package Peridot\Runner
 */
class Runner
{
    use HasEventEmitterTrait;

    /**
     * @var \Peridot\Core\Suite
     */
    protected $suite;

    /**
     * @var \Peridot\Configuration
     */
    protected $configuration;

    /**
     * @param Suite $suite
     * @param Configuration $configuration
     * @param EventEmitterInterface $eventEmitter
     */
    public function __construct(Suite $suite, Configuration $configuration, EventEmitterInterface $eventEmitter)
    {
        $this->suite = $suite;
        $this->configuration = $configuration;
        $this->eventEmitter = $eventEmitter;
    }

    /**
     * Run the Suite
     *
     * @param SpecResult $result
     */
    public function run(SpecResult $result)
    {
        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
           $this->eventEmitter->emit('error', [$errno, $errstr, $errfile, $errline]);
        });

        $this->eventEmitter->on('spec.failed', function ($spec, $e) {
            if ($this->configuration->shouldStopOnFailure()) {
                $this->eventEmitter->emit('suite.halt');
            }
        });

        $this->eventEmitter->emit('runner.start');
        $this->suite->setEventEmitter($this->eventEmitter);
        $this->suite->run($result);
        $this->eventEmitter->emit('runner.end');

        restore_error_handler();
    }
}
