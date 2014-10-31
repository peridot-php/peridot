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
     * {@inheritdoc}
     *
     * @param callable $setupFn
     */
    public function addSetupFunction(callable $setupFn)
    {
        $this->setUpFns[] = $setupFn;
    }

    /**
     * {@inheritdoc}
     *
     * @param callable $tearDownFn
     */
    public function addTearDownFunction(callable $tearDownFn)
    {
        $this->tearDownFns[] = $tearDownFn;
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
     * Bind the suite's callables and scopes to the provided test
     *
     * @param $test
     */
    public function bindTest(TestInterface $test)
    {
        $childScopes = $this->getScope()->peridotGetChildScopes();
        foreach ($childScopes as $scope) {
            $test->getScope()->peridotAddChildScope($scope);
        }
        foreach ($this->setUpFns as $fn) {
            $test->addSetupFunction($fn);
        }
        foreach ($this->tearDownFns as $fn) {
            $test->addTearDownFunction($fn);
        }
    }

    /**
     * Run a test and track its results.
     *
     * @param TestResult $result
     * @param $test
     */
    protected function runTest(AbstractTest $test, TestResult $result)
    {
        if (!is_null($this->getPending())) {
            $test->setPending($this->getPending());
        }

        $this->bindTest($test);
        $test->setEventEmitter($this->eventEmitter);
        $test->run($result);
    }
}
