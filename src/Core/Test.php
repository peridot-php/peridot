<?php

namespace Peridot\Core;

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
            $definition = function () {
                //noop
            };
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

        $this->executeTest($result);

        $this->runTearDown();
        $result->endTest($this);
    }

    /**
     * Attempt to execute setup functions and run the test definition
     *
     * @param TestResult $result
     */
    protected function executeTest(TestResult $result)
    {
        try {
            $this->runSetup();
            call_user_func($this->getDefinition());
            $result->passTest($this);
        } catch (Exception $e) {
            $result->failTest($this, $e);
        }
    }

    /**
     * Excecute the test's setup functions
     */
    protected function runSetup()
    {
        $this->forEachNodeTopToBottom(function (TestInterface $node) {
            $setups = $node->getSetupFunctions();
            foreach ($setups as $setup) {
                $setup();
            }
        });
    }

    /**
     * Execute this test's tear down functions.
     */
    protected function runTearDown()
    {
        $this->forEachNodeBottomToTop(function (TestInterface $test) {
            $tearDowns = $test->getTearDownFunctions();
            foreach ($tearDowns as $tearDown) {
                try {
                    $tearDown();
                } catch (Exception $e) {
                    continue;
                }
            }
        });
    }

    /**
     * Execute a callback for each node in this test, starting
     * at the bottom of the tree.
     *
     * @param callable $fn
     */
    protected function forEachNodeBottomToTop(callable $fn)
    {
        $node = $this;
        while (!is_null($node)) {
            $fn($node);
            $node = $node->getParent();
        }
    }

    /**
     * Execute a callback for each node in this test, starting
     * at the top of the tree.
     *
     * @param callable $fn
     */
    protected function forEachNodeTopToBottom(callable $fn)
    {
        $node = $this;
        $nodes = [];
        while (!is_null($node)) {
            array_unshift($nodes, $node);
            $node = $node->getParent();
        }
        foreach ($nodes as $node) {
            $fn($node);
        }
    }
}
