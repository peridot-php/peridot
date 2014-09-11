<?php
class Spec
{
    protected $definition;

    protected $setUpFns = [];

    protected $tearDownFns = [];

    public function __construct($description, callable $definition)
    {
        $this->definition = $definition;
    }

    public function run()
    {
        $result = new SpecResult();
        $result->startSpec();

        foreach ($this->setUpFns as $fn) {
            $fn();
        }
        $bound = \Closure::bind($this->definition, $this, $this);
        $bound();
        foreach ($this->tearDownFns as $fn) {
            $fn();
        }

        return $result;
    }

    public function addSetUpFunction(callable $setupFn)
    {
        $this->setUpFns[] = \Closure::bind($setupFn, $this, $this);
    }

    public function addTearDownFunction(callable $tearDownFn)
    {
        $this->tearDownFns[] = \Closure::bind($tearDownFn, $this, $this);
    }
}
