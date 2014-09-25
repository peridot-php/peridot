<?php
use Peridot\Runner\Context;
use Peridot\Core\Suite;
use Peridot\Core\Spec;

/**
 * Creates a suite and sets it on the suite factory
 *
 * @param string $description
 * @param callable $fn
 */
function describe($description, callable $fn) {
    Context::getInstance()->describe($description, $fn);
}

/**
 * Identical to describe. Useful for test readability
 *
 * @param $description
 * @param callable $fn
 */
function context($description, callable $fn) {
    describe($description, $fn);
}

/**
 * Create a spec and add it to the current suite
 *
 * @param $description
 * @param $fn
 */
function it($description, callable $fn) {
    Context::getInstance()->it($description, $fn);
}

/**
 * Create a pending suite
 *
 * @param $description
 * @param callable $fn
 */
function xdescribe($description, callable $fn) {
    Context::getInstance()->describe($description, $fn, true);
}

/**
 * Create a pending context
 *
 * @param $description
 * @param callable $fn
 */
function xcontext($description, callable $fn) {
    xdescribe($description, $fn);
}

/**
 * Create a pending spec
 *
 * @param $description
 * @param callable $fn
 */
function xit($description, callable $fn) {
    $spec = Context::getInstance()->it($description, $fn);
    $spec->setPending(true);
}

/**
 * Add a setup function for all specs in the
 * current suite
 *
 * @param callable $fn
 */
function beforeEach(callable $fn) {
    Context::getInstance()->beforeEach($fn);
}

/**
 * Change default assert behavior to throw exceptions
 */
assert_options(ASSERT_WARNING, false);
assert_options(ASSERT_CALLBACK, function($script, $line, $message, $description) {
    throw new Exception($description);
});
