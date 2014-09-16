<?php
namespace Peridot\Runner;
use Peridot\Core\Suite;

/**
 * Class Context tracks the state of the runner - i.e the current suite
 * @package Peridot\Runner
 */
class Context
{
    /**
     * @var \Peridot\Core\Suite
     */
    protected $suite;

    /**
     * @var Context
     */
    private static $instance = null;

    /**
     * Private constructor
     */
    private function __construct()
    {
    }

    /**
     * Set the current suite context
     *
     * @param Suite $suite
     */
    public function setCurrentSuite(Suite $suite)
    {
        $this->suite = $suite;
    }

    /**
     * @return Suite
     */
    public function getCurrentSuite()
    {
        return $this->suite;
    }

    /**
     * Singleton access to SuiteFactory
     *
     * @return Context
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }
}

