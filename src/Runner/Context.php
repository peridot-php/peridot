<?php
namespace Peridot\Runner;
use Peridot\Core\Test;
use Peridot\Core\Suite;

/**
 * Context tracks the state of the runner - i.e the current Suite, and provides access to
 * Peridot's global state.
 *
 * @package Peridot\Runner
 */
class Context
{
    /**
     * @var array
     */
    protected $suites;

    /**
     * @var Context
     */
    private static $instance = null;

    /**
     * Private constructor
     */
    private function __construct()
    {
        $this->suites = [new Suite("", function () {})];
    }

    /**
     * @return \Peridot\Core\Suite
     */
    public function getCurrentSuite()
    {
        return $this->suites[0];
    }

    /**
     * Creates a suite and adds it to the current suite. The newly
     * created suite will become the new "current" suite
     *
     * @param $description
     * @param callable $fn
     */
    public function addSuite($description, callable $fn, $pending = null)
    {
        $suite = new Suite($description, $fn);
        if (!is_null($pending)) {
            $suite->setPending($pending);
        }
        $this->getCurrentSuite()->addTest($suite);
        array_unshift($this->suites, $suite);
        call_user_func($suite->getDefinition());
        array_shift($this->suites);

        return $suite;
    }

    /**
     * Create a test and add it to the current suite
     *
     * @param $description
     * @param $fn
     */
    public function addTest($description, callable $fn, $pending = null)
    {
        $test = new Test($description, $fn);
        if (!is_null($pending)) {
            $test->setPending($pending);
        }
        $this->getCurrentSuite()->addTest($test);

        return $test;
    }

    /**
     * Add a setup function for all tests in the
     * current suite
     *
     * @param callable $fn
     */
    public function addSetupFunction(callable $fn)
    {
        $this->getCurrentSuite()->addSetupFunction($fn);
    }

    /**
     * Add a tear down function for all tests in the current suite
     *
     * @param callable $fn
     */
    public function addTearDownFunction(callable $fn)
    {
        $this->getCurrentSuite()->addTearDownFunction($fn);
    }

    /**
     * Singleton access to Context
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
