<?php
namespace Peridot\Runner;

use Evenement\EventEmitterInterface;
use Peridot\Configuration;
use Peridot\Core\SpecResult;
use Peridot\Core\Suite;
use Evenement\EventEmitterTrait;

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
        set_error_handler(function($errno, $errstr, $errfile, $errline) {
           $this->eventEmitter->emit('error', [$errno, $errstr, $errfile, $errline]);
        });

        $this->eventEmitter->on('spec:failed', function($spec, $e) {
            if ($this->configuration->shouldStopOnFailure()) {
                $this->eventEmitter->emit('halt');
            }
            $this->eventEmitter->emit('fail', [$spec, $e]);
        });

        $this->eventEmitter->on('spec:passed', function($spec) {
            $this->eventEmitter->emit('pass', [$spec]);
        });

        $this->eventEmitter->on('spec:pending', function($spec) {
            $this->eventEmitter->emit('pending', [$spec]);
        });

        $this->eventEmitter->emit('start');
        $this->suite->setEventEmitter($this->eventEmitter);
        $this->suite->run($result);
        $this->eventEmitter->emit('end');

        restore_error_handler();
    }
}
