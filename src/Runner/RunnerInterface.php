<?php
namespace Peridot\Runner;

use Peridot\Core\TestResult;

/**
 * The RunnerInterface defines how a runner should run tests
 * and populate results.
 *
 * @package Peridot\Runner
 */
interface RunnerInterface
{
    /**
     * Run the Suite
     *
     * @param TestResult $result
     */
    public function run(TestResult $result);
}
