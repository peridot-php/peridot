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
     * Execute the test along with any setup and tear down functions.
     *
     * @param  TestResult $result
     * @return void
     */
    public function run(TestResult $result)
    {
        $result->startTest();

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

        $boundTest = $this->getBoundDefinition();
        try {
            call_user_func($boundTest);
            $result->passTest($this);
        } catch (\Exception $e) {
            $result->failTest($this, $e);
        }

        $this->runTearDown();
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

    /**
     * Returns the test's definition bound to
     * the test's scope
     *
     * @return Closure
     */
    protected function getBoundDefinition()
    {
        return Closure::bind(
            $this->definition,
            $this->scope,
            $this->scope
        );
    }
}
