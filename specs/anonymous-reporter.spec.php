<?php
use Evenement\EventEmitter;
use Peridot\Configuration;
use Peridot\Core\Suite;
use Peridot\Reporter\AnonymousReporter;
use Peridot\Reporter\ReporterInterface;
use Peridot\Runner\Runner;

describe('AnonymousReporter', function() {

    beforeEach(function() {
        $this->configuration = new Configuration();
        $this->runner = new Runner(new Suite("test", function() {}), $this->configuration, new EventEmitter());
        $this->output = new Symfony\Component\Console\Output\NullOutput();
    });

    it('should call the init function passed in', function() {
        $configuration = null;
        $runner = null;
        $output = null;
        new AnonymousReporter(function(ReporterInterface $reporter) use (&$configuration, &$runner, &$output) {
            $configuration = $reporter->getConfiguration();
            $runner = $reporter->getRunner();
            $output = $reporter->getOutput();
        }, $this->configuration, $this->runner, $this->output, new EventEmitter());
        assert(
            !is_null($configuration) && !is_null($runner) && !is_null($output),
            'configuration, runner, and output should not be null'
        );
    });

});
