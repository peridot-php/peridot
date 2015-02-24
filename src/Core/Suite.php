<?php
namespace Peridot\Core;

/**
 * Suites organize tests and other suites.
 *
 * @package Peridot\Core
 */
class Suite extends AbstractTest
{
    /**
     * Tests belonging to this suite
     *
     * @var array
     */
    protected $tests = [];

    /**
     * Has the suite been halted
     *
     * @var bool
     */
    protected $halted = false;

    /**
     * Add a test to the suite
     *
     * @param Test $test
     */
    public function addTest(TestInterface $test)
    {
        $test->setParent($this);
        $this->tests[] = $test;
    }

    /**
     * Return collection of tests
     *
     * @return array
     */
    public function getTests()
    {
        return $this->tests;
    }

    /**
     * Set suite tests
     *
     * @param array $tests
     */
    public function setTests(array $tests)
    {
        $this->tests = $tests;
    }

    /**
     * Execute the Suite definition.
     *
     * @return void
     */
    public function define()
    {
        $this->eventEmitter->emit('suite.define', [$this]);
        call_user_func_array($this->getDefinition(), $this->getDefinitionArguments());
    }

    /**
     * Run all the specs belonging to the suite
     *
     * @param TestResult $result
     */
    public function run(TestResult $result)
    {
        $this->eventEmitter->emit('suite.start', [$this]);
        $this->eventEmitter->on('suite.halt', [$this, 'halt']);

        foreach ($this->tests as $test) {

            if ($this->halted) {
                break;
            }

            $this->runTest($test, $result);
        }
        $this->eventEmitter->emit('suite.end', [$this]);
    }

    /**
     * Put the Suite in a halted state. A halted Suite will not run or will
     * stop running if currently running.
     *
     * @return void
     */
    public function halt()
    {
        $this->halted = true;
    }

    /**
     * Run a test and track its results.
     *
     * @param TestResult $result
     * @param $test
     */
    protected function runTest(AbstractTest $test, TestResult $result)
    {
        if ($this->getPending() !== null) {
            $test->setPending($this->getPending());
        }

        $test->setEventEmitter($this->eventEmitter);
        $test->run($result);
    }
}
