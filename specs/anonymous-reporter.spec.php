<?php
use Evenement\EventEmitter;
use Peridot\Configuration;
use Peridot\Core\Suite;
use Peridot\Reporter\AnonymousReporter;
use Peridot\Reporter\ReporterInterface;
use Peridot\Runner\Runner;

describe('AnonymousReporter', function() {

    beforeEach(function() {
        $this->eventEmitter = new EventEmitter();
        $this->configuration = new Configuration();
        $this->output = new Symfony\Component\Console\Output\NullOutput();
    });

    it('should call the init function passed in', function() {
        $configuration = null;
        $runner = null;
        $output = null;
        $emitter = null;
        new AnonymousReporter(function(ReporterInterface $reporter) use (&$configuration, &$output, &$emitter) {
            $configuration = $reporter->getConfiguration();
            $output = $reporter->getOutput();
            $emitter = $reporter->getEventEmitter();
        }, $this->configuration, $this->output, $this->eventEmitter);
        assert(
            !is_null($configuration) && !is_null($output) && !is_null($emitter),
            'configuration, output, and emitter should not be null'
        );
    });

});
