<?php
namespace Peridot\Runner;

use Evenement\EventEmitter;
use Peridot\Core\HasEventEmitterTrait;
use Peridot\Core\Test;
use Peridot\Core\Suite;

/**
 * Context tracks the state of the runner - i.e the current Suite, and provides access to
 * Peridot's global state.
 *
 * @package Peridot\Runner
 */
final class Context
{
    use HasEventEmitterTrait;

    /**
     * @var array
     */
    protected $suites;

    /**
     * @var string
     */
    protected $file;

    /**
     * @var Context
     */
    private static $instance = null;

    /**
     * Private constructor
     */
    private function __construct()
    {
        $this->clear();
    }

    /**
     * Clear the internal suite structure.
     *
     * @return void
     */
    public function clear()
    {
        $this->suites = [new Suite("", function () {
            //noop
        }, false)];
    }

    /**
     * Set the file for the context. This file
     * generally represents the current file being used
     * to load suites.
     *
     * @param $path
     * @return void
     */
    public function setFile($path)
    {
        $this->file = $path;
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->file;
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
     * @param string $description
     * @param callable $fn
     * @param bool|null $pending
     * @param bool $focused
     */
    public function addSuite($description, callable $fn, $pending = null, $focused = false)
    {
        $suite = $this->createSuite($description, $fn, $pending, $focused);

        $this->getCurrentSuite()->addTest($suite);
        array_unshift($this->suites, $suite);
        $suite->define();
        array_shift($this->suites);

        return $suite;
    }

    /**
     * Create a test and add it to the current suite
     *
     * @param string $description
     * @param callable $fn
     * @param bool|null $pending
     * @param bool $focused
     */
    public function addTest($description, callable $fn = null, $pending = null, $focused = false)
    {
        $test = new Test($description, $fn, $focused);
        if ($pending !== null) {
            $test->setPending($pending);
        }
        $test->setFile($this->file);
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
        if (self::$instance === null) {
            self::$instance = new Context();
        }

        return self::$instance;
    }

    /**
     * Create a Suite based on the state of the Context.
     *
     * @param string $description
     * @param callable $fn
     * @param bool|null $pending
     * @param bool $focused
     * @return Suite
     */
    private function createSuite($description, callable $fn, $pending, $focused)
    {
        $suite = new Suite($description, $fn, $focused);
        if ($pending !== null) {
            $suite->setPending($pending);
        }
        $suite->setFile($this->file);
        $suite->setEventEmitter($this->getEventEmitter());
        return $suite;
    }
}
