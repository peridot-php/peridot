<?php
use Evenement\EventEmitter;
use Peridot\Configuration;
use Peridot\Reporter\AbstractBaseReporter;
use Peridot\Reporter\CompositeReporter;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\NullOutput;

describe('CompositeReporter', function() {

    beforeEach(function() {
        $this->configuration = new Configuration();
        $this->output = new BufferedOutput();
        $this->emitter = new EventEmitter();
        $this->reporterA = new FakeReporter($this->configuration, new BufferedOutput(), $this->emitter);
        $this->reporterB = new FakeReporter($this->configuration, new NullOutput(), $this->emitter);
        $this->reporterC = new FakeReporter($this->configuration, new BufferedOutput(), $this->emitter);
        $this->reporters = [$this->reporterA, $this->reporterB, $this->reporterC];
        $this->reporter = new CompositeReporter($this->reporters, $this->configuration, $this->output, $this->emitter);
    });

    context('->setEventEmitter()', function() {
        beforeEach(function () {
            $this->emitter2 = new EventEmitter();
            $this->reporter->setEventEmitter($this->emitter2);
        });

        it('should set the event emitter', function() {
            assert($this->reporter->getEventEmitter() === $this->emitter2, 'should be the same event emitter');
        });

        it('should set the event emitter for each wrapped reporter', function() {
            assert($this->reporterA->getEventEmitter() === $this->emitter2, 'should be the same event emitter');
            assert($this->reporterB->getEventEmitter() === $this->emitter2, 'should be the same event emitter');
            assert($this->reporterC->getEventEmitter() === $this->emitter2, 'should be the same event emitter');
        });
    });

    context('when runner.end is emitted', function() {
        it('should include an error number and the test description', function() {
            $this->emitter->emit('runner.end', [1.0]);
            $content = $this->output->fetch();
            $expected = implode([
                PHP_EOL,
                spl_object_hash($this->reporterA),
                PHP_EOL,
                PHP_EOL,
                spl_object_hash($this->reporterC),
                PHP_EOL
            ]);
            assert($content === $expected, 'output should contain wrapped reporter output');
        });
    });

});

class FakeReporter extends AbstractBaseReporter
{
    public function init()
    {
        $this->getOutput()->writeln(spl_object_hash($this));
    }
}
