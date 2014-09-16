<?php
namespace Peridot\Core;

/**
 * Class Spec - maps to it() style functions
 * @package Peridot\Core
 */
class Spec extends AbstractSpec
{
    /**
     * Execute the spec along with any setup and tear down
     * functions
     *
     * @param SpecResult $result
     */
    public function run(SpecResult $result)
    {
        $result->startSpec();

        foreach ($this->setUpFns as $fn) {
            $fn();
        }

        $bound = \Closure::bind($this->definition, $this, $this);
        try {
            $bound();
        } catch (\Exception $e) {
            $result->failSpec();
        }

        foreach ($this->tearDownFns as $fn) {
            $fn();
        }
    }
}
