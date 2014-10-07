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

        if ($this->getPending()) {
            $result->pendSpec($this);
            return;
        }

        foreach ($this->setUpFns as $setUp) {
            try {
                $setUp();
            } catch (Exception $e) {
                $result->failSpec($this, $e);
                $this->runTearDown();
                return;
            }
        }

        $boundSpec = $this->getBoundDefinition();
        try {
            call_user_func($boundSpec);
            $result->passSpec($this);
        } catch (\Exception $e) {
            $result->failSpec($this, $e);
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

    /**
     * Returns the spec's definition bound to
     * the spec's scope
     *
     * @return Closure
     */
    protected function getBoundDefinition()
    {
        return Closure::bind(
            $this->definition,
            $this->peridotScopeVariableDoNotTouchThanks,
            $this->peridotScopeVariableDoNotTouchThanks
        );
    }
}
