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
    }

    public function indent()
    {
        return implode('  ', array_fill(0, $this->column + 1, ''));
    }
}
