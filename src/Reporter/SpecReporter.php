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
     * @var array
     */
    protected $errors = [];

    /**
     * Initialize reporter. Setup and listen for runner events
     *
     * @return void
     */
    public function init()
    {
        $root = Context::getInstance()->getCurrentSuite();

        $this->runner->on('start', function() {
            $this->output->writeln("");
        });

        $this->runner->on('suite:start', function(Suite $suite) use ($root) {
            if ($suite != $root) {
                ++$this->column;
                $this->output->writeln(sprintf('%s<fg=cyan>%s</fg=cyan>', $this->indent(), $suite->getDescription()));
            }
        });

        $this->runner->on('suite:end', function() {
            --$this->column;
            if ($this->column == 0) {
                $this->output->writeln("");
            }
        });

        $this->runner->on('pass', function(Spec $spec) {
            $this->output->writeln(sprintf("  %s<fg=green>%s</fg=green> %s", $this->indent(), '✓', $spec->getDescription()));
        });

        $this->runner->on('fail', function(Spec $spec, \Exception $e) {
            $this->output->writeln(sprintf("  %s<fg=red>%d) %s</fg=red>", $this->indent(), count($this->errors), $spec->getDescription()));
        });

        $this->runner->on('end', function() {
            $this->output->writeln(sprintf("\n  <fg=green>%d passing</fg=green>", $this->passing));
            if ($this->errors) {
                $this->output->writeln(sprintf("  <fg=red>%d failing</fg=red>", count($this->errors)));
            }
            $this->output->writeln("");
            for ($i = 0; $i < count($this->errors); $i++) {
                list($spec, $error) = $this->errors[$i];
                $this->output->writeln(sprintf("  %d)%s:", $i + 1, $spec->getTitle()));
                $this->output->writeln(sprintf("     <fg=red>%s</fg=red>", $error->getMessage()));
                $trace = preg_replace('/^#/m', "      #", $error->getTraceAsString());
                $this->output->writeln(sprintf("%s\n", $trace));
            }
        });
    }

    public function indent()
    {
        return implode('  ', array_fill(0, $this->column + 1, ''));
    }
}
