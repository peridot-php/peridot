<?php

namespace Peridot\Core;

use Evenement\EventEmitterInterface;

/**
 * SpecResults tracks passing, pending, and failing specs.
 *
 * @package Peridot\Core
 */
class SpecResult
{
    use HasEventEmitterTrait;

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
     * Tracks total number of pending specs run against this result
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
     * Returns a summary string containing total specs run and total specs
     * failed
     *
     * @return string
     */
    public function getSummary()
    {
        $summary = sprintf('%d run, %d failed', $this->specCount, $this->failureCount);
        if ($this->pendingCount > 0) {
            $summary .= sprintf(', %d pending', $this->pendingCount);
        }

        return $summary;
    }

    /**
     * Fail the given spec.
     *
     * @param SpecInterface $spec
     */
    public function failSpec(SpecInterface $spec, \Exception $e)
    {
        $this->failureCount++;
        $this->eventEmitter->emit('spec.failed', [$spec, $e]);
    }

    /**
     * Notify result that spec is pending
     *
     * @param SpecInterface $spec
     */
    public function pendSpec(SpecInterface $spec)
    {
        $this->pendingCount++;
        $this->eventEmitter->emit('spec.pending', [$spec]);
    }

    /**
     * Pass the given spec.
     *
     * @param SpecInterface $spec
     */
    public function passSpec(SpecInterface $spec)
    {
        $this->eventEmitter->emit('spec.passed', [$spec]);
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

    /**
     * @return int
     */
    public function getPendingCount()
    {
        return $this->pendingCount;
    }
}
