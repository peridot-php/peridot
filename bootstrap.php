<?php
use Peridot\Runner\SuiteFactory;
use Peridot\Core\Suite;
use Peridot\Core\Spec;

/**
 * Creates a suite and sets it on the suite factory
 *
 * @param string $description
 * @param callable $fn
 */
function describe($description, callable $fn) {
    $singleton = SuiteFactory::getInstance();
    $suite = new Suite($description, $fn);
    $singleton->setCurrentSuite($suite);
    call_user_func($suite->getDefinition());
}

/**
 * Create a spec and add it to the current suite
 *
 * @param $description
 * @param $fn
 */
function it($description, callable $fn) {
    $singleton = SuiteFactory::getInstance();
    $suite = $singleton->getCurrentSuite();
    $spec = new Spec($description, $fn);
    $suite->addSpec($spec);
}

/**
 * Add a setup function for all specs in the
 * current suite
 *
 * @param callable $fn
 */
function beforeEach(callable $fn) {
    $singleton = SuiteFactory::getInstance();
    $suite = $singleton->getCurrentSuite();
    $suite->addSetUpFunction($fn);
}

/**
 * Change default assert behavior to throw exceptions
 */
assert_options(ASSERT_WARNING, false);
assert_options(ASSERT_CALLBACK, function($script, $line, $message) {
    throw new Exception($message);
});
