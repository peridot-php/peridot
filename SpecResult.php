<?php

class SpecResult
{
    protected $specCount = 0;
    protected $failureCount = 0;

    public function getSummary()
    {
        return sprintf('%d run, %d failed', $this->specCount, $this->failureCount);
    }

    public function failSpec()
    {
        $this->failureCount++;
    }

    public function startSpec()
    {
        $this->specCount++;
    }
}

