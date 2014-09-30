<?php
use Peridot\Core\Suite;
use Peridot\Reporter\AnonymousReporter;
use Peridot\Runner\Runner;

describe('AnonymousReporter', function() {

    beforeEach(function() {
        $this->runner = new Runner(new Suite("test", function() {}));
        $this->output = new Symfony\Component\Console\Output\NullOutput();
    });

    it('should call the init function passed in', function() {
        $runner = null;
        $output = null;
        $reporter = new AnonymousReporter(function($r, $o) use (&$runner, &$output) {
            $runner = $r;
            $output = $o;
        }, $this->runner, $this->output);
        assert(!is_null($runner) && !is_null($output), 'runner and output should not be null');
    });

});
