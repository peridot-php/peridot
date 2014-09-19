<?php

namespace Peridot\Core;

use Closure;
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

        foreach ($this->setUpFns as $setUp) {
            try {
                $setUp();
            } catch (Exception $e) {
                $result->failSpec($this);
                $this->runTearDown();
                return;
            }
        }

        $boundSpec = Closure::bind($this->definition, $this, $this);
        try {
            $boundSpec();
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
        foreach ($this->tearDownFns as $tearDown) {
            try {
                $tearDown();
            } catch (Exception $e) {
                continue;
            }
        }
    }
}
