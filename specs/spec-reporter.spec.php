<?php
use Evenement\EventEmitter;
use Peridot\Configuration;
use Peridot\Core\Test;
use Peridot\Reporter\SpecReporter;
use Symfony\Component\Console\Output\BufferedOutput;

describe('SpecReporter', function() {

    beforeEach(function() {
        $config = new Configuration();

        $this->output = new BufferedOutput();
        $this->emitter = new EventEmitter();
        $this->reporter = new SpecReporter($config, $this->output, $this->emitter);
    });

    context('when test.failed is emitted', function() {
        it('should include an error number and the test description', function() {
            $test = new Test("test", function() {});
            $this->emitter->emit('test.failed', [$test]);
            $contents = $this->output->fetch();
            assert(strstr($contents, '1) test') !== false, "error count and test description should be present");
        });
    });

    context('when test.pending is emitted', function() {
        it('should include an error number and the test description', function() {
            $test = new Test("test", function() {});
            $this->emitter->emit('test.pending', [$test]);
            $contents = $this->output->fetch();
            assert(strstr($contents, '- test') !== false, "dash and test description should be present");
        });
    });

});
