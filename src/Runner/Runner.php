<?php
namespace Peridot\Runner;

use Peridot\Core\SpecResult;

class Runner
{
    public function runSpec($path)
    {
        include $path;
        $this->result = new SpecResult();
        SuiteFactory::getInstance()->getSuite()->run($this->result);
    }

    public function getResult()
    {
        return $this->result;
    }
}
