<?php
namespace Peridot\Core;

class Suite
{
    protected $specs = [];

    public function add(Spec $spec)
    {
        $this->specs[] = $spec;
    }

    public function run(SpecResult $result)
    {
        foreach ($this->specs as $spec) {
            $spec->run($result);
        }
    }
}
