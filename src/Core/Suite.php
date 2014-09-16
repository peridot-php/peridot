<?php
namespace Peridot\Core;

/**
 * Class Suite maps to describe() style functions as well as context() style functions
 * @package Peridot\Core
 */
class Suite extends AbstractSpec
{
    /**
     * Specs belonging to this suite
     *
     * @var array
     */
    protected $specs = [];

    /**
     * Add a spec to the suite
     *
     * @param Spec $spec
     */
    public function addSpec(Spec $spec)
    {
        $this->specs[] = $spec;
    }

    /**
     * Run all the specs belonging to the suite
     *
     * @param SpecResult $result
     */
    public function run(SpecResult $result)
    {
        foreach ($this->specs as $spec) {
            $spec->run($result);
        }
    }
}
