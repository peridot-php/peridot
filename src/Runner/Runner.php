<?php
namespace Peridot\Runner;

use Peridot\Core\SpecResult;
use Peridot\Core\Suite;

/**
 * Class Runner
 * @package Peridot\Runner
 */
class Runner
{
    /**
     * @var \Peridot\Core\Suite
     */
    protected $suite;

    /**
     * Constructor
     *
     * @param SpecResult $result
     */
    public function __construct(Suite $suite)
    {
        $this->suite = $suite;
    }

    /**
     * @param SpecResult $result
     */
    public function run(SpecResult $result)
    {
        $this->suite->run($result);
    }
}
