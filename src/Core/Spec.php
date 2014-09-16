<?php
namespace Peridot\Core;

/**
 * Class Spec - maps to it() style functions
 * @package Peridot\Core
 */
class Spec
{
    /**
     * The spec definition as a callable
     *
     * @var callable
     */
    protected $definition;

    /**
     * A collection of functions to run
     * before specs execute
     *
     * @var array
     */
    protected $setUpFns = [];

    /**
     * A collection of functions to run
     * after specs execute
     *
     * @var array
     */
    protected $tearDownFns = [];

    /**
     * Constructor
     *
     * @param string $description
     * @param callable $definition
     */
    public function __construct($description, callable $definition)
    {
        $this->definition = $definition;
    }

    /**
     * Execute the spec along with any setup and tear down
     * functions
     *
     * @param SpecResult $result
     */
    public function run(SpecResult $result)
    {
        $result->startSpec();

        foreach ($this->setUpFns as $fn) {
            $fn();
        }

        $bound = \Closure::bind($this->definition, $this, $this);
        try {
            $bound();
        } catch (\Exception $e) {
            $result->failSpec();
        }

        foreach ($this->tearDownFns as $fn) {
            $fn();
        }
    }

    /**
     * Add a function to execute before the spec runs
     *
     * @param callable $setupFn
     */
    public function addSetUpFunction(callable $setupFn)
    {
        $this->setUpFns[] = \Closure::bind($setupFn, $this, $this);
    }

    /**
     * Add a function to execute after the spec runs
     *
     * @param callable $tearDownFn
     */
    public function addTearDownFunction(callable $tearDownFn)
    {
        $this->tearDownFns[] = \Closure::bind($tearDownFn, $this, $this);
    }
}
