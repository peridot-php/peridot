<?php

namespace Peridot\Core;

use Exception;

/**
 * Class Spec - maps to it() style functions
 * @package Peridot\Core
 */
class Spec extends AbstractSpec
{
    /**
     * Execute the spec along with any setup and tear down functions.
     *
     * @param SpecResult $result
     * @return void
     */
    public function run(SpecResult $result)
    {
        $result->startSpec();

        foreach ($this->setUpFns as $fn) {
            try {
                $fn();
            } catch (Exception $e) {
                $result->failSpec($this);
                $this->runTearDown();
            }
        }

        $bound = \Closure::bind($this->definition, $this, $this);
        try {
            $bound();
            $result->passSpec($this);
        } catch (Exception $e) {
            $result->failSpec($this);
        }

        $this->runTearDown();
    }

    /**
     * Execute this spec's tear down functions.
     */
    protected function runTearDown()
    {
        foreach ($this->tearDownFns as $fn) {
            $fn();
        }
    }
}
