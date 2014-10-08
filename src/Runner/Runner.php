<?php
namespace Peridot\Runner;

use Evenement\EventEmitterInterface;
use Peridot\Configuration;
use Peridot\Core\SpecResult;
use Peridot\Core\Suite;

/**
 * Class Runner
 * @package Peridot\Runner
 */
class Runner
{
    /**
     * @var \Peridot\Core\Suite
     */
    protected $suite;

    /**
     * @var \Peridot\Configuration
     */
    protected $configuration;

    /**
     * @var \Evenement\EventEmitterInterface
     */
    protected $eventEmitter;

    /**
     * Constructor
     *
     * @param SpecResult $result
     */
    public function __construct(Suite $suite, Configuration $configuration, EventEmitterInterface $eventEmitter)
    {
        $this->suite = $suite;
        $this->configuration = $configuration;
        $this->eventEmitter = $eventEmitter;
    }

    /**
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
