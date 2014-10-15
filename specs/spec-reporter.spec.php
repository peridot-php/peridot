<?php
use Evenement\EventEmitter;
use Peridot\Configuration;
use Peridot\Core\Test;
use Peridot\Reporter\SpecReporter;
use Symfony\Component\Console\Output\BufferedOutput;

describe('SpecReporter', function() {

    beforeEach(function() {
        $this->configuration = new Configuration();
        $this->output = new BufferedOutput();
        $this->emitter = new EventEmitter();
        $this->reporter = new SpecReporter($this->configuration, $this->output, $this->emitter);
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

    describe('->color()', function() {
        context('when colors are disabled', function() {
            it('should return plain text', function() {
                $this->configuration->disableColors();
                $text = $this->reporter->color('color', 'hello world');
                assert($text == "hello world", "disabled colors should contain color sequences");
            });
        });
    });

});
