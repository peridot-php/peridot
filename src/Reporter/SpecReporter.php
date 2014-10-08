<?php
namespace Peridot\Reporter;

use Peridot\Core\Spec;
use Peridot\Core\Suite;
use Peridot\Runner\Context;

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

        $this->eventEmitter->on('runner.start', function() {
            $this->output->writeln("");
        });

        $this->eventEmitter->on('suite.start', function(Suite $suite) use ($root) {
            if ($suite != $root) {
                ++$this->column;
                $this->output->writeln(sprintf('%s%s', $this->indent(), $suite->getDescription()));
            }
        });

        $this->eventEmitter->on('suite.end', function() {
            --$this->column;
            if ($this->column == 0) {
                $this->output->writeln("");
            }
        });

        $this->eventEmitter->on('spec.passed', function(Spec $spec) {
            $this->output->writeln(sprintf(
                "  %s%s %s",
                $this->indent(),
                $this->color('success', $this->symbol('check')),
                $this->color('muted', $spec->getDescription())
            ));
        });

        $this->eventEmitter->on('spec.failed', function(Spec $spec, \Exception $e) {
            $this->output->writeln(sprintf(
                "  %s%s",
                $this->indent(),
                $this->color('error', sprintf("%d) %s", count($this->errors), $spec->getDescription()))
            ));
        });

        $this->eventEmitter->on('spec.pending', function(Spec $spec) {
            $this->output->writeln(sprintf(
                $this->color('pending', "  %s- %s"),
                $this->indent(),
                $spec->getDescription()
            ));
        });

        $this->eventEmitter->on('runner.end', function() {
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

    /**
     * Output result footer
     */
    public function footer()
    {
        $this->output->write($this->color('success', sprintf("\n  %d passing", $this->passing)));
        $this->output->writeln(sprintf($this->color('muted', " (%s)"), \PHP_Timer::timeSinceStartOfRequest()));
        if ($this->errors) {
            $this->output->writeln($this->color('error', sprintf("  %d failing", count($this->errors))));
        }
        if ($this->pending) {
            $this->output->writeln($this->color('pending', sprintf("  %d pending", $this->pending)));
        }
        $this->output->writeln("");
        for ($i = 0; $i < count($this->errors); $i++) {
            list($spec, $error) = $this->errors[$i];
            $this->output->writeln(sprintf("  %d)%s:", $i + 1, $spec->getTitle()));
            $this->output->writeln($this->color('error', sprintf("     %s", $error->getMessage())));
            $trace = preg_replace('/^#/m', "      #", $error->getTraceAsString());
            $this->output->writeln($this->color('muted', $trace));
        }
    }
}
