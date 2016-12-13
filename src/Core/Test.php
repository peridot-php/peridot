<?php

namespace Peridot\Core;

use Error;
use ErrorException;
use Exception;
use Throwable;

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
    public function __construct($description, callable $definition = null, $focused = false)
    {
        if ($definition === null) {
            $this->pending = true;
            $definition = function () {
                //noop
            };
        }
        parent::__construct($description, $definition, $focused);
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function isFocused()
    {
        return $this->focused;
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
        $result->endTest($this);
    }

    /**
     * Attempt to execute setup functions and run the test definition
     *
     * @param TestResult $result
     */
    protected function executeTest(TestResult $result)
    {
        $action = ['passTest', $this];
        $handler = $this->handleErrors($result, $action);
        try {
            $this->runSetup();
            call_user_func_array($this->getDefinition(), $this->getDefinitionArguments());
        } catch (Throwable $e) {
            $this->failIfPassing($action, $e);
        } catch (Exception $e) {
            $this->failIfPassing($action, $e);
        }
        $this->runTearDown($result, $action);
        $this->restoreErrorHandler($handler);
    }

    /**
     * Excecute the test's setup functions
     */
    protected function runSetup()
    {
        $this->forEachNodeTopDown(function (TestInterface $node) {
            $setups = $node->getSetupFunctions();
            foreach ($setups as $setup) {
                $setup();
            }
        });
    }

    /**
     * Run the tests tear down methods and have the result
     * perform the method indicated by $action
     *
     * @param TestResult $result
     * @param array $action
     */
    protected function runTearDown(TestResult $result, array $action)
    {
        $this->forEachNodeBottomUp(function (TestInterface $test) use ($result, &$action) {
            $tearDowns = $test->getTearDownFunctions();
            foreach ($tearDowns as $tearDown) {
                try {
                    $tearDown();
                } catch (Throwable $e) {
                    $this->failIfPassing($action, $e);
                } catch (Exception $e) {
                    $this->failIfPassing($action, $e);
                }
            }
        });
        call_user_func_array([$result, $action[0]], array_slice($action, 1));
    }

    /**
     * Set an error handler to handle errors within the test
     *
     * @param TestResult $result
     * @param array      &$action
     *
     * @return callable|null
     */
    protected function handleErrors(TestResult $result, array &$action)
    {
        $handler = null;
        $handler = set_error_handler(function ($severity, $message, $path, $line) use ($result, &$action, &$handler) {
            $arguments = func_get_args();

            // if there is an existing error handler, call it and record the result
            $isHandled = $handler && false !== call_user_func_array($handler, $arguments);

            if (!$isHandled) {
                $result->getEventEmitter()->emit('error', $arguments);

                // honor the error reporting configuration - this also takes care of the error control operator (@)
                $errorReporting = error_reporting();
                $shouldHandle = $severity === ($severity & $errorReporting);

                if ($shouldHandle) {
                    $this->failIfPassing($action, new ErrorException($message, 0, $severity, $path, $line));
                }
            }
        });

        return $handler;
    }

    /**
     * Restore the previous error handler
     *
     * @param callable|null $handler
     */
    protected function restoreErrorHandler($handler)
    {
        if ($handler) {
            set_error_handler($handler);
        } else {
            // unfortunately, we can't pass null until PHP 5.5
            set_error_handler(function () { return false; });
        }
    }

    /**
     * Fail the test, but do not overwrite existing failures
     *
     * @param array &$action
     * @param mixed $error
     */
    protected function failIfPassing(array &$action, $error)
    {
        if ('passTest' === $action[0]) {
            $action = ['failTest', $this, $error];
        }
    }
}
