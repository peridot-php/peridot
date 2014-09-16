<?php
namespace Peridot\Runner;

use Peridot\Core\SpecResult;

/**
 * Class Runner
 * @package Peridot\Runner
 */
class Runner
{
    /**
     * @var \Peridot\Core\SpecResult
     */
    protected $result;

    /**
     * Constructor
     *
     * @param SpecResult $result
     */
    public function __construct(SpecResult $result)
    {
        $this->result = $result;
    }

    /**
     * Run the spec at the given path
     *
     * @param string $path
     */
    public function runSpec($path)
    {
        include $path;
        SuiteFactory::getInstance()->getCurrentSuite()->run($this->result);
    }

    /**
     * Return the result of the runner
     *
     * @return SpecResult
     */
    public function getResult()
    {
        return $this->result;
    }
}
