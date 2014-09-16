<?php
use Peridot\Runner\SuiteFactory;
use Peridot\Core\Suite;
use Peridot\Core\Spec;

/**
 * @param $description
 * @param callable $fn
 */
function describe($description, callable $fn) {
    $singleton = SuiteFactory::getInstance();
    $suite = new Suite();
    $singleton->setSuite($suite);
    $fn();
}

/**
 * @param $description
 * @param $fn
 */
function it($description, $fn) {
    $singleton = SuiteFactory::getInstance();
    $suite = $singleton->getSuite();
    $spec = new Spec($description, $fn);
    $suite->addSpec($spec);
}

/**
 * Change default assert behavior to throw exceptions
 */
assert_options(ASSERT_WARNING, false);
assert_options(ASSERT_CALLBACK, function($script, $line, $message) {
    throw new Exception($message);
});