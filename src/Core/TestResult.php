<?php

namespace Peridot\Core;

use Evenement\EventEmitterInterface;

/**
 * TestResults tracks passing, pending, and failing tests.
 *
 * @package Peridot\Core
 */
class TestResult
{
    use HasEventEmitterTrait;

    /**
     * Tracks total tests run against this result
     *
     * @var int
     */
    protected $testCount = 0;

    /**
     * Tracks total number of failed tests run against this result
     *
     * @var int
     */
    protected $failureCount = 0;

    /**
     * Tracks total number of pending tests run against this result
     *
     * @var int
     */
    protected $pendingCount = 0;

    /**
     * @param EventEmitterInterface $eventEmitter
     */
    public function __construct(EventEmitterInterface $eventEmitter)
    {
        $this->eventEmitter = $eventEmitter;
    }

    /**
     * Returns a summary string containing total tests run and total tests
     * failed
     *
     * @return string
     */
    public function getSummary()
    {
        $summary = sprintf('%d run, %d failed', $this->testCount, $this->failureCount);
        if ($this->pendingCount > 0) {
            $summary .= sprintf(', %d pending', $this->pendingCount);
        }

        return $summary;
    }

    /**
     * Fail the given test.
     *
     * @param TestInterface $test
     */
    public function failTest(TestInterface $test, \Exception $e)
    {
        $this->failureCount++;
        $this->eventEmitter->emit('spec.failed', [$test, $e]);
    }

    /**
     * Notify result that test is pending
     *
     * @param TestInterface $test
     */
    public function pendTest(TestInterface $test)
    {
        $this->pendingCount++;
        $this->eventEmitter->emit('spec.pending', [$test]);
    }

    /**
     * Pass the given test.
     *
     * @param TestInterface $test
     */
    public function passTest(TestInterface $test)
    {
        $this->eventEmitter->emit('spec.passed', [$test]);
    }

    /**
     * Increment the test count
     */
    public function startTest()
    {
        $this->testCount++;
    }

    /**
     * @return int
     */
    public function getFailureCount()
    {
        return $this->failureCount;
    }

    /**
     * @return int
     */
    public function getTestCount()
    {
        return $this->testCount;
    }

    /**
     * @return int
     */
    public function getPendingCount()
    {
        return $this->pendingCount;
    }
}