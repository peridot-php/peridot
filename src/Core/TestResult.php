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
     * True if focused specs were configured via DSL functions
     *
     * @var bool
     */
    protected $isFocusedByDsl = false;

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
     * @param mixed $exception
     */
    public function failTest(TestInterface $test, $exception)
    {
        $this->failureCount++;
        $this->eventEmitter->emit('test.failed', [$test, $exception]);
    }

    /**
     * Notify result that test is pending
     *
     * @param TestInterface $test
     */
    public function pendTest(TestInterface $test)
    {
        $this->pendingCount++;
        $this->eventEmitter->emit('test.pending', [$test]);
    }

    /**
     * Pass the given test.
     *
     * @param TestInterface $test
     */
    public function passTest(TestInterface $test)
    {
        $this->eventEmitter->emit('test.passed', [$test]);
    }

    /**
     * Increment test count and emit start event
     *
     * @param TestInterface $test
     */
    public function startTest(TestInterface $test)
    {
        $this->testCount++;
        $this->eventEmitter->emit('test.start', [$test]);
    }

    /**
     * Emit end event for a test
     *
     * @param TestInterface $test
     */
    public function endTest(TestInterface $test)
    {
        $this->eventEmitter->emit('test.end', [$test]);
    }

    /**
     * Get the number of failures tracked by this result.
     *
     * @return int
     */
    public function getFailureCount()
    {
        return $this->failureCount;
    }

    /**
     * Set the number of failures tracked by this result.
     *
     * @param int $failureCount
     */
    public function setFailureCount($failureCount)
    {
        $this->failureCount = $failureCount;
        return $this;
    }

    /**
     * Get the number of tests tracked by this
     * result.
     *
     * @return int
     */
    public function getTestCount()
    {
        return $this->testCount;
    }

    /**
     * Set the number of tests tracked by this
     * result.
     *
     * @param int $testCount
     */
    public function setTestCount($testCount)
    {
        $this->testCount = $testCount;
        return $this;
    }

    /**
     * Get the number of pending tests tracked
     * by this test result.
     *
     * @return int
     */
    public function getPendingCount()
    {
        return $this->pendingCount;
    }

    /**
     * Set the number of pending tests tracked
     * by this test result.
     *
     * @param int $pendingCount
     */
    public function setPendingCount($pendingCount)
    {
        $this->pendingCount = $pendingCount;
        return $this;
    }

    /**
     * Returns true if focused specs were configured via DSL functions
     *
     * @return bool
     */
    public function isFocusedByDsl()
    {
        return $this->isFocusedByDsl;
    }

    /**
     * Mark this result as having focused specs configured via DSL functions
     *
     * @param bool $isFocusedByDsl
     */
    public function setIsFocusedByDsl($isFocusedByDsl)
    {
        $this->isFocusedByDsl = $isFocusedByDsl;
        return $this;
    }
}
