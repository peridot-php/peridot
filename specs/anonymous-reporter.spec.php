<?php
use Peridot\Configuration;
use Peridot\Core\Suite;
use Peridot\Reporter\AnonymousReporter;
use Peridot\Runner\Runner;

describe('AnonymousReporter', function() {

    beforeEach(function() {
        $this->configuration = new Configuration();
        $this->runner = new Runner(new Suite("test", function() {}));
        $this->output = new Symfony\Component\Console\Output\NullOutput();
    });

    it('should call the init function passed in', function() {
        $configuration = null;
        $runner = null;
        $output = null;
        $reporter = new AnonymousReporter(function($c, $r, $o) use (&$configuration, &$runner, &$output) {
            $configuration = $c;
            $runner = $r;
            $output = $o;
        }, $this->configuration, $this->runner, $this->output);
        assert(
            !is_null($configuration) && !is_null($runner) && !is_null($output),
            'configuration, runner, and output should not be null'
        );
    });

});
