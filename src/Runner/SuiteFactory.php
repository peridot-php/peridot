<?php
namespace Peridot\Runner;
use Peridot\Core\Suite;

/**
 * Class SuiteFactory tracks the state of the runner - i.e the current suite
 * @package Peridot\Runner
 */
class SuiteFactory
{
    /**
     * @var \Peridot\Core\Suite
     */
    protected $suite;

    /**
     * @var SuiteFactory
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
    public function setSuite(Suite $suite)
    {
        $this->suite = $suite;
    }

    /**
     * @return Suite
     */
    public function getSuite()
    {
        return $this->suite;
    }

    /**
     * Singleton access to SuiteFactory
     *
     * @return SuiteFactory
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }
}

