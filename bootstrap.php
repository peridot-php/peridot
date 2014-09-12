<?php

use Peridot\Runner\SuiteFactory;
use Peridot\Core\Suite;
use Peridot\Core\Spec;

function describe($description, callable $fn) {
    $singleton = SuiteFactory::getInstance();
    $suite = new Suite();
    $singleton->setSuite($suite);
    $fn();
}

function it($description, $fn) {
    $singleton = SuiteFactory::getInstance();
    $suite = $singleton->getSuite();
    $spec = new Spec($description, $fn);
    $suite->add($spec);
}
