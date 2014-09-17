<?php

namespace Peridot\Core;

/**
 * Class SpecResult
 * @package Peridot\Core
 */
class SpecResult
{
    /**
     * Tracks total specs run against this result
     *
     * @var int
     */
    protected $specCount = 0;

    /**
     * Tracks total number of failed specs run against this result
     *
     * @var int
     */
    protected $failureCount = 0;

    /**
     * Returns a summary string containing total specs run and total specs
     * failed
     *
     * @return string
     */
    public function getSummary()
    {
        return sprintf('%d run, %d failed', $this->specCount, $this->failureCount);
    }

    /**
     * Increment the failure count
     */
    public function failSpec()
    {
        $this->failureCount++;
    }

    /**
     * Increment the spec count
     */
    public function startSpec()
    {
        $this->specCount++;
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
    public function getSpecCount()
    {
        return $this->specCount;
    }
}
