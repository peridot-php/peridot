<?php

use Evenement\EventEmitter;
use Peridot\Core\Spec;
use Peridot\Core\SpecResult;
use Peridot\Core\Suite;
use Peridot\Test\ItWasRun;

describe("SpecResult", function() {

    beforeEach(function() {
        $this->eventEmitter = new EventEmitter();
    });

    it("should return the number of tests run", function() {
        $result = new SpecResult($this->eventEmitter);
        $suite = new Suite("Suite", function() {});
        $suite->setEventEmitter($this->eventEmitter);
        $suite->addspec(new ItWasRun("this was run", function () {}));
        $suite->addspec(new ItWasRun("this was also run", function () {}));
        $suite->run($result);
        assert($result->getSpecCount() === 2, "two specs should have run");
    });

    it("should return the number of tests failed", function() {
        $result = new SpecResult($this->eventEmitter);
        $suite = new Suite("Suite", function() {});
        $suite->setEventEmitter($this->eventEmitter);
        $suite->addspec(new ItWasRun("this was run", function () {}));
        $suite->addspec(new ItWasRun("this was also run", function () {}));
        $suite->addspec(new ItWasRun("this failed", function () {
            throw new Exception('spec failed');
        }));
        $suite->run($result);
        assert($result->getFailureCount() === 1, "one specs should have failed");
    });

    describe("->failSpec()", function() {
        beforeEach(function() {
            $this->eventEmitter = new EventEmitter();
            $this->result = new SpecResult($this->eventEmitter);
        });

        it('should emit a spec:failed event', function() {
            $emitted = null;
            $exception = null;
            $this->eventEmitter->on('spec.failed', function ($spec, $e) use (&$emitted, &$exception){
                $emitted = $spec;
                $exception = $e;
            });

            $spec = new Spec('spec', function() {});
            $e = new \Exception("failure");
            $this->result->failSpec($spec, $e);
            assert($emitted === $spec && $exception != null, 'should have emitted spec:failed event with a spec and exception');
       });
    });

    describe("->passSpec()", function() {
        beforeEach(function() {
            $this->emitter = new EventEmitter();
            $this->result = new SpecResult($this->emitter);
        });

        it('should emit a spec:passed event', function() {
            $emitted = null;
            $this->emitter->on('spec.passed', function ($spec) use (&$emitted){
                $emitted = $spec;
            });

            $spec = new Spec('spec', function() {});
            $this->result->passSpec($spec);
            assert($emitted === $spec, 'should have emitted spec:passed event');
        });
    });

    describe("->pendSpec()", function() {
        beforeEach(function() {
            $this->emitter = new EventEmitter();
            $this->result = new SpecResult($this->emitter);
        });

        it('should emit a spec:pending event', function() {
            $emitted = null;
            $this->emitter->on('spec.pending', function ($spec) use (&$emitted){
                $emitted = $spec;
            });

            $spec = new Spec('spec', function() {});
            $spec->setPending(true);
            $this->result->pendSpec($spec);
            assert($emitted === $spec, 'should have emitted spec:pending event');
        });
    });
});
