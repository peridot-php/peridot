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
    public function addSetUpFunction(callable $setupFn);

    /**
     * Return all registered setup functions
     *
     * @return array
     */
    public function getSetUpFunctions();

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
     * Return scope for this test. Scope contains instance variables
     * for a spec
     *
     * @return Scope
     */
    public function getScope();

    /**
     * @param  EventEmitterInterface $emitter
     * @return mixed
     */
    public function setEventEmitter(EventEmitterInterface $emitter);

    /**
     * @return EventEmitterInterface
     */
    public function getEventEmitter();
}
