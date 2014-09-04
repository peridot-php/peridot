<?php
class Spec
{
    protected $definition;

    protected $setupFns = [];

    public function __construct($description, callable $definition)
    {
        $this->definition = $definition;
    }

    public function run()
    {
        foreach ($this->setupFns as $fn) {
            $fn();
        }
        $bound = \Closure::bind($this->definition, $this, $this);
        $bound();
    }

    public function addSetupFunction(callable $setupFn)
    {
        $this->setupFns[] = \Closure::bind($setupFn, $this, $this);
    }
}
