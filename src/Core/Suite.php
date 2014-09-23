<?php
namespace Peridot\Core;
use Evenement\EventEmitterTrait;

/**
 * Class Suite maps to describe() style functions as well as context() style functions
 * @package Peridot\Core
 */
class Suite extends AbstractSpec
{
    /**
     * Specs belonging to this suite
     *
     * @var array
     */
    protected $specs = [];

    /**
     * Add a spec to the suite
     *
     * @param Spec $spec
     */
    public function addSpec(SpecInterface $spec)
    {
        $spec->setParent($this);
        $this->specs[] = $spec;
    }

    /**
     * Return collection of specs
     *
     * @return array
     */
    public function getSpecs()
    {
        return $this->specs;
    }

    /**
     * {@inheritdoc}
     *
     * @param callable $setupFn
     */
    public function addSetUpFunction(callable $setupFn)
    {
        $this->setUpFns[] = $setupFn;
    }

    /**
     * {@inheritdoc}
     *
     * @param callable $tearDownFn
     */
    public function addTearDownFunction(callable $tearDownFn)
    {
        $this->tearDownFns[] = $tearDownFn;
    }

    /**
     * Run all the specs belonging to the suite
     *
     * @param SpecResult $result
     */
    public function run(SpecResult $result)
    {
        $this->emit('suite:start', [$this]);
        foreach ($this->specs as $spec) {

            $spec->on('suite:start', function($suite) {
                $this->emit('suite:start', [$suite]);
            });

            $spec->on('suite:end', function($suite) {
               $this->emit('suite:end', [$suite]);
            });

            $this->bindCallables($spec);
            $spec->run($result);
        }
        $this->emit('suite:end', [$this]);
    }

    /**
     * Bind the suite's callables to the provided spec
     *
     * @param $spec
     */
    public function bindCallables(SpecInterface $spec)
    {
        foreach ($this->setUpFns as $fn) {
            $spec->addSetUpFunction($fn);
        }
        foreach ($this->tearDownFns as $fn) {
            $spec->addTearDownFunction($fn);
        }
    }
}
