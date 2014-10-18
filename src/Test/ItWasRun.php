<?php
namespace Peridot\Test;

use Peridot\Core\Test;

/**
 * ItWasRun - the first of the tests. Before there was Peridot, there was ItWasRun
 *
 * @package Peridot\Test
 */
class ItWasRun extends Test
{
    /**
     * @param string   $description
     * @param callable $definition
     */
    public function __construct($description, callable $definition)
    {
        parent::__construct($description, $definition);
        $this->getScope()->wasRun = false;
        $this->getScope()->log = false;
    }

    public function wasRun()
    {
        return $this->getScope()->wasRun;
    }

    public function log()
    {
        return $this->getScope()->log;
    }
}
