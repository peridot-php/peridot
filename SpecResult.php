<?php

class SpecResult
{
    protected $count = 0;

    public function getSummary()
    {
        return sprintf('%d run, 0 failed', $this->count);
    }
    
    public function startSpec()
    {
        $this->count++;
    }
}

