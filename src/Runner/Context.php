<?php
namespace Peridot\Runner;
use Peridot\Core\Spec;
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
     * Creates a suite and sets it on the suite factory
     *
     * @param $description
     * @param callable $fn
     */
    public function describe($description, callable $fn)
    {
        $suite = new Suite($description, $fn);
        $this->setCurrentSuite($suite);
        call_user_func($suite->getDefinition());
    }

    /**
     * Create a spec and add it to the current suite
     *
     * @param $description
     * @param $fn
     */
    public function it($description, callable $fn)
    {
        $suite = $this->getCurrentSuite();
        $spec = new Spec($description, $fn);
        $suite->addSpec($spec);
    }

    /**
     * Add a setup function for all specs in the
     * current suite
     *
     * @param callable $fn
     */
    public function beforeEach(callable $fn)
    {
        $suite = $this->getCurrentSuite();
        $suite->addSetUpFunction($fn);
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

