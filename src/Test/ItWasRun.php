<?php
namespace Peridot\Test;

use Peridot\Core\Spec;

/**
 * Class ItWasRun the first of the specs
 * @package Peridot\Test
 */
class ItWasRun extends Spec
{
    /**
     * @var string
     */
    public $log = "";

    /**
     * @var bool
     */
    public $wasRun = false;
}
