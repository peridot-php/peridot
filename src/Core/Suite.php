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
     * {@inheritdoc}
     *
     * @return bool
     */
    public function isFocused()
    {
        if ($this->focused === true) {
            return $this->focused;
        }

        foreach ($this->tests as $test) {
            if ($test->isFocused()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Set the focused status of the test and its children according to the
     * supplied focus pattern and/or skip pattern
     *
     * @param string|null $focusPattern
     * @param string|null $skipPattern
     */
    public function applyFocusPatterns($focusPattern, $skipPattern = null)
    {
        parent::applyFocusPatterns($focusPattern, $skipPattern);

        foreach ($this->tests as $test) {
            $test->applyFocusPatterns($focusPattern, $skipPattern);
        }
    }

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

        foreach ($this->getTestsToRun() as $test) {
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
     * @param TestInterface $test
     * @param TestResult $result
     */
    protected function runTest(TestInterface $test, TestResult $result)
    {
        if ($this->getPending() !== null) {
            $test->setPending($this->getPending());
        }

        $test->setEventEmitter($this->eventEmitter);
        $test->run($result);
    }

    /**
     * Get the subset of the defined tests that should actually be run.
     *
     * @return array
     */
    protected function getTestsToRun()
    {
        $tests = array_filter($this->tests, function (TestInterface $test) {
            return $test->isFocused();
        });

        return empty($tests) ? $this->tests : $tests;
    }
}
