<?php
namespace Peridot\Runner;

use Evenement\EventEmitterInterface;
use Peridot\Configuration;
use Peridot\Core\HasEventEmitterTrait;
use Peridot\Core\TestResult;
use Peridot\Core\Suite;

/**
 * The Runner is responsible for running a given Suite.
 *
 * @package Peridot\Runner
 */
class Runner implements RunnerInterface
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
     * {@inheritdoc}
     *
     * @param TestResult $result
     */
    public function run(TestResult $result)
    {
        $this->applyFocus($result);

        $this->eventEmitter->on('test.failed', function () {
            if ($this->configuration->shouldStopOnFailure()) {
                $this->eventEmitter->emit('suite.halt');
            }
        });

        $this->eventEmitter->emit('runner.start');
        $this->suite->setEventEmitter($this->eventEmitter);
        $start = microtime(true);
        $this->suite->run($result);
        $this->eventEmitter->emit('runner.end', [microtime(true) - $start, $result]);
    }

    private function applyFocus(TestResult $result)
    {
        $result->setIsFocusedByDsl($this->suite->isFocused());
        $focusPattern = $this->configuration->getFocusPattern();
        $skipPattern = $this->configuration->getSkipPattern();

        if ($focusPattern !== null || $skipPattern !== null) {
            $this->suite->applyFocusPatterns($focusPattern, $skipPattern);
        }
    }
}
