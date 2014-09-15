<?php
namespace Peridot\Runner;

use Peridot\Core\SpecResult;

class Runner
{
    protected $result;

    public function __construct(SpecResult $result)
    {
        $this->result = $result;
    }

    public function runSpec($path)
    {
        include $path;
        SuiteFactory::getInstance()->getSuite()->run($this->result);
    }

    public function getResult()
    {
        return $this->result;
    }
}
