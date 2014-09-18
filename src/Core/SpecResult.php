<?php

namespace Peridot\Core;

use Evenement\EventEmitterTrait;

/**
 * Class SpecResult
 * @package Peridot\Core
 */
class SpecResult
{
    use EventEmitterTrait;

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
     *
     * @param SpecInterface $spec
     */
    public function failSpec(SpecInterface $spec)
    {
        $this->failureCount++;
        $this->emit('spec:failed', [$spec]);
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
