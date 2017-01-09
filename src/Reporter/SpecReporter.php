<?php
namespace Peridot\Reporter;

use Peridot\Core\Suite;
use Peridot\Core\Test;
use Peridot\Core\TestResult;
use Peridot\Runner\Context;

/**
 * The SpecReporter is the default Peridot reporter. It organizes Suite and Test results
 * in a hierarchical manner.
 *
 * @package Peridot\Reporter
 */
class SpecReporter extends AbstractBaseReporter
{
    /**
     * @var int
     */
    protected $column = 0;

    /**
     * @var \Peridot\Core\Suite
     */
    protected $root;

    /**
     * Initialize reporter. Setup and listen for runner events
     *
     * @return void
     */
    public function init()
    {
        $this->root = Context::getInstance()->getCurrentSuite();

        $this->eventEmitter->on('runner.start', [$this, 'onRunnerStart']);
        $this->eventEmitter->on('suite.start', [$this, 'onSuiteStart']);
        $this->eventEmitter->on('suite.end', [$this, 'onSuiteEnd']);
        $this->eventEmitter->on('test.passed', [$this, 'onTestPassed']);
        $this->eventEmitter->on('test.failed', [$this, 'onTestFailed']);
        $this->eventEmitter->on('test.pending', [$this, 'onTestPending']);
        $this->eventEmitter->on('runner.end', [$this, 'onRunnerEnd']);
    }

    public function onRunnerStart()
    {
        $this->output->writeln("");
    }

    /**
     * @param Suite $suite
     */
    public function onSuiteStart(Suite $suite)
    {
        if ($suite != $this->root) {
            ++$this->column;
            $this->output->writeln(sprintf('%s%s', $this->indent(), $suite->getDescription()));
        }
    }

    public function onSuiteEnd()
    {
        --$this->column;
        if ($this->column == 0) {
            $this->output->writeln("");
        }
    }

    /**
     * @param Test $test
     */
    public function onTestPassed(Test $test)
    {
        $this->output->writeln(sprintf(
            "  %s%s %s",
            $this->indent(),
            $this->color('success', $this->symbol('check')),
            $this->color('muted', $test->getDescription())
        ));
    }

    /**
     * @param Test $test
     */
    public function onTestFailed(Test $test)
    {
        $this->output->writeln(sprintf(
            "  %s%s",
            $this->indent(),
            $this->color('error', sprintf("%d) %s", count($this->errors), $test->getDescription()))
        ));
    }

    /**
     * @param Test $test
     */
    public function onTestPending(Test $test)
    {
        $this->output->writeln(sprintf(
            $this->color('pending', "  %s- %s"),
            $this->indent(),
            $test->getDescription()
        ));
    }

    /**
     * @param float $time
     * @param TestResult $result
     */
    public function onRunnerEnd($time, TestResult $result)
    {
        $this->footer();
        $this->warnings($result);
    }

    /**
     * Returns the current indent for the spec reporter
     *
     * @return string
     */
    public function indent()
    {
        return implode('  ', array_fill(0, $this->column + 1, ''));
    }
}
