<?php
namespace Peridot\Reporter;

use Peridot\Core\Test;
use Peridot\Core\Suite;
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
     * Initialize reporter. Setup and listen for runner events
     *
     * @return void
     */
    public function init()
    {
        $root = Context::getInstance()->getCurrentSuite();

        $this->eventEmitter->on('runner.start', function () {
            $this->output->writeln("");
        });

        $this->eventEmitter->on('suite.start', function (Suite $suite) use ($root) {
            if ($suite != $root) {
                ++$this->column;
                $this->output->writeln(sprintf('%s%s', $this->indent(), $suite->getDescription()));
            }
        });

        $this->eventEmitter->on('suite.end', function () {
            --$this->column;
            if ($this->column == 0) {
                $this->output->writeln("");
            }
        });

        $this->eventEmitter->on('test.passed', function (Test $test) {
            $this->output->writeln(sprintf(
                "  %s%s %s",
                $this->indent(),
                $this->color('success', $this->symbol('check')),
                $this->color('muted', $test->getDescription())
            ));
        });

        $this->eventEmitter->on('test.failed', function (Test $test, \Exception $e) {
            $this->output->writeln(sprintf(
                "  %s%s",
                $this->indent(),
                $this->color('error', sprintf("%d) %s", count($this->errors), $test->getDescription()))
            ));
        });

        $this->eventEmitter->on('test.pending', function (Test $test) {
            $this->output->writeln(sprintf(
                $this->color('pending', "  %s- %s"),
                $this->indent(),
                $test->getDescription()
            ));
        });

        $this->eventEmitter->on('runner.end', function () {
            $this->footer();
        });
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
