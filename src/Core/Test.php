<?php

namespace Peridot\Core;

use Closure;
use Exception;

/**
 * The main test fixture for Peridot.
 *
 * @package Peridot\Core
 */
class Test extends AbstractTest
{
    /**
     * @param string $description
     * @param callable $definition
     */
    public function __construct($description, callable $definition = null)
    {
        if (is_null($definition)) {
            $this->pending = true;
            $definition = function() { }; //noop
        }
        parent::__construct($description, $definition);
    }

    /**
     * Execute the test along with any setup and tear down functions.
     *
     * @param  TestResult $result
     * @return void
     */
    public function run(TestResult $result)
    {
        $result->startTest($this);

        if ($this->getPending()) {
            $result->pendTest($this);

            return;
        }

        foreach ($this->setUpFns as $setUp) {
            try {
                $setUp();
            } catch (Exception $e) {
                $result->failTest($this, $e);
                $this->runTearDown();

                return;
            }
        }

        try {
            call_user_func($this->getDefinition());
            $result->passTest($this);
        } catch (\Exception $e) {
            $result->failTest($this, $e);
        }

        $this->runTearDown();
        $result->endTest($this);
    }

    /**
     * Execute this test's tear down functions.
     */
    protected function runTearDown()
    {
        foreach ($this->tearDownFns as $tearDown) {
            try {
                $tearDown();
            } catch (Exception $e) {
                continue;
            }
        }
    }
}
