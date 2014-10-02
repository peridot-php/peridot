<?php
namespace Peridot\Runner;

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
    use EventEmitterTrait;

    /**
     * @var \Peridot\Core\Suite
     */
    protected $suite;

    /**
     * @var \Peridot\Configuration
     */
    protected $configuration;

    /**
     * Constructor
     *
     * @param SpecResult $result
     */
    public function __construct(Suite $suite, Configuration $configuration)
    {
        $this->suite = $suite;
        $this->configuration = $configuration;
    }

    /**
     * @param SpecResult $result
     */
    public function run(SpecResult $result)
    {
        $result->on('spec:failed', function($spec, $e) {
            if ($this->configuration->shouldStopOnFailure()) {
                $this->suite->emit('halt');
            }
            $this->emit('fail', [$spec, $e]);
        });

        $result->on('spec:passed', function($spec) {
            $this->emit('pass', [$spec]);
        });

        $result->on('spec:pending', function($spec) {
            $this->emit('pending', [$spec]);
        });

        $this->suite->on('suite:start', function($suite) {
            $this->emit('suite:start', [$suite]);
        });

        $this->suite->on('suite:end', function($suite) {
            $this->emit('suite:end', [$suite]);
        });

        $this->emit('start');
        $this->suite->run($result);
        $this->emit('end');
    }
}
