<?php
use Peridot\Core\Context;

/**
 * Creates a suite and sets it on the suite factory
 *
 * @param string $description
 * @param callable $fn
 */
function describe($description, callable $fn)
{
    Context::getInstance()->addSuite($description, $fn);
}

/**
 * Identical to describe. Useful for test readability
 *
 * @param $description
 * @param callable $fn
 */
function context($description, callable $fn)
{
    describe($description, $fn);
}

/**
 * Create a spec and add it to the current suite
 *
 * @param $description
 * @param $fn
 */
function it($description, callable $fn = null)
{
    Context::getInstance()->addTest($description, $fn);
}

/**
 * Create a pending suite
 *
 * @param $description
 * @param callable $fn
 */
function xdescribe($description, callable $fn)
{
    Context::getInstance()->addSuite($description, $fn, true);
}

/**
 * Create a pending context
 *
 * @param $description
 * @param callable $fn
 */
function xcontext($description, callable $fn)
{
    xdescribe($description, $fn);
}

/**
 * Create a pending spec
 *
 * @param $description
 * @param callable $fn
 */
function xit($description, callable $fn = null)
{
    Context::getInstance()->addTest($description, $fn, true);
}

/**
 * Add a setup function for all specs in the
 * current suite
 *
 * @param callable $fn
 */
function beforeEach(callable $fn)
{
    Context::getInstance()->addSetupFunction($fn);
}

/**
 * Add a tear down function for all specs in the
 * current suite
 *
 * @param callable $fn
 */
function afterEach(callable $fn)
{
    Context::getInstance()->addTearDownFunction($fn);
}

/**
 * Change default assert behavior to throw exceptions
 */
assert_options(ASSERT_WARNING, false);
assert_options(ASSERT_CALLBACK, function ($script, $line, $message, $description) {
    throw new Exception($description);
});
