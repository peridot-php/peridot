<?php
namespace Peridot\Runner;

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

