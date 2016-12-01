<?php
namespace Peridot\Core;

use Evenement\EventEmitterInterface;

/**
 * Defines the contract for Peridot test fixtures like Test and Suite
 *
 * @package Peridot\Core
 */
interface TestInterface
{
    /**
     * @param  TestResult $result
     * @return mixed
     */
    public function run(TestResult $result);

    /**
     * Add a function to execute before the test runs
     *
     * @param callable $setupFn
     */
    public function addSetupFunction(callable $setupFn);

    /**
     * Return all registered setup functions
     *
     * @return array
     */
    public function getSetupFunctions();

    /**
     * Add a function to execute after the test runs
     *
     * @param callable $tearDownFn
     */
    public function addTearDownFunction(callable $tearDownFn);

    /**
     * Return all registered tear down functions
     *
     * @return array
     */
    public function getTearDownFunctions();

    /**
     * @return string
     */
    public function getDescription();

    /**
     * Returns the callable definition of the TestInterface
     *
     * @return callable
     */
    public function getDefinition();

    /**
     * @return TestInterface
     */
    public function getParent();

    /**
     * @param  TestInterface $parent
     * @return mixed
     */
    public function setParent(TestInterface $parent);

    /**
     * Returns the full description including parent descriptions
     *
     * @return string
     */
    public function getTitle();

    /**
     * Return whether or not the test is pending
     *
     * @return bool|null
     */
    public function getPending();

    /**
     * Set the pending status of the test
     *
     * @param  bool $state
     * @return void
     */
    public function setPending($state);

    /**
     * Return whether or not the test is focused
     *
     * @return bool
     */
    public function isFocused();

    /**
     * Set the focused status of the test and its children according to the
     * supplied focus pattern and/or skip pattern
     *
     * @param string|null $focusPattern
     * @param string|null $skipPattern
     */
    public function applyFocusPatterns($focusPattern, $skipPattern = null);

    /**
     * Return scope for this test. Scope contains instance variables
     * for a spec
     *
     * @return Scope
     */
    public function getScope();

    /**
     * Set the scope object for a test
     *
     * @param Scope $scope
     * @return mixed
     */
    public function setScope(Scope $scope);

    /**
     * @param  EventEmitterInterface $emitter
     * @return mixed
     */
    public function setEventEmitter(EventEmitterInterface $emitter);

    /**
     * @return EventEmitterInterface
     */
    public function getEventEmitter();

    /**
     * Execute a callback for each node in this test, starting
     * at the bottom of the tree.
     *
     * @param callable $fn
     */
    public function forEachNodeBottomUp(callable $fn);

    /**
     * Execute a callback for each node in this test, starting
     * at the top of the tree.
     *
     * @param callable $fn
     */
    public function forEachNodeTopDown(callable $fn);

    /**
     * Set arguments to be passed to the test definition when invoked.
     *
     * @param array $args
     * @return mixed
     */
    public function setDefinitionArguments(array $args);

    /**
     * Return an array of arguments to be passed to the test definition when invoked.
     *
     * @return array
     */
    public function getDefinitionArguments();
}
