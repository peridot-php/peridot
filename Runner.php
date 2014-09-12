<?php
class Runner
{
    public function runSpec($path)
    {
        include $path;
        $this->result = new SpecResult();
        SuiteFactory::getInstance()->getSuite()->run($this->result);
    }

    public function getResult()
    {
        return $this->result;
    }
}

class SuiteFactory
{
    protected $suite;

    private static $instance = null;

    private function __construct()
    {
    }

    public function setSuite($suite)
    {
        $this->suite = $suite;
    }

    public function getSuite()
    {
        return $this->suite;
    }

    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }
}

function describe($description, callable $fn) {
    $singleton = SuiteFactory::getInstance();
    $suite = new Suite();
    $singleton->setSuite($suite);
    $fn();
}

function it($description, $fn) {
    $singleton = SuiteFactory::getInstance();
    $suite = $singleton->getSuite();
    $spec = new Spec($description, $fn);
    $suite->add($spec);
}
