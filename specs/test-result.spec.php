<?php

use Evenement\EventEmitter;
use Peridot\Core\Test;
use Peridot\Core\TestResult;
use Peridot\Core\Suite;
use Peridot\Test\ItWasRun;

describe("TestResult", function() {

    beforeEach(function() {
        $this->eventEmitter = new EventEmitter();
    });

    it("should return the number of tests run", function() {
        $result = new TestResult($this->eventEmitter);
        $suite = new Suite("Suite", function() {});
        $suite->setEventEmitter($this->eventEmitter);
        $suite->addTest(new ItWasRun("this was run", function () {}));
        $suite->addTest(new ItWasRun("this was also run", function () {}));
        $suite->run($result);
        assert($result->getTestCount() === 2, "two specs should have run");
    });

    it("should return the number of tests failed", function() {
        $result = new TestResult($this->eventEmitter);
        $suite = new Suite("Suite", function() {});
        $suite->setEventEmitter($this->eventEmitter);
        $suite->addTest(new ItWasRun("this was run", function () {}));
        $suite->addTest(new ItWasRun("this was also run", function () {}));
        $suite->addTest(new ItWasRun("this failed", function () {
            throw new Exception('spec failed');
        }));
        $suite->run($result);
        assert($result->getFailureCount() === 1, "one specs should have failed");
    });

    describe("->failTest()", function() {
        beforeEach(function() {
            $this->eventEmitter = new EventEmitter();
            $this->result = new TestResult($this->eventEmitter);
        });

        it('should emit a spec:failed event', function() {
            $emitted = null;
            $exception = null;
            $this->eventEmitter->on('test.failed', function ($test, $e) use (&$emitted, &$exception){
                $emitted = $test;
                $exception = $e;
            });

            $test = new Test('spec', function() {});
            $e = new \Exception("failure");
            $this->result->failTest($test, $e);
            assert($emitted === $test && $exception != null, 'should have emitted spec:failed event with a spec and exception');
       });
    });

    describe("->passTest()", function() {
        beforeEach(function() {
            $this->emitter = new EventEmitter();
            $this->result = new TestResult($this->emitter);
        });

        it('should emit a spec:passed event', function() {
            $emitted = null;
            $this->emitter->on('test.passed', function ($test) use (&$emitted){
                $emitted = $test;
            });

            $test = new Test('spec', function() {});
            $this->result->passTest($test);
            assert($emitted === $test, 'should have emitted spec:passed event');
        });
    });

    describe("->pendTest()", function() {
        beforeEach(function() {
            $this->emitter = new EventEmitter();
            $this->result = new TestResult($this->emitter);
        });

        it('should emit a spec:pending event', function() {
            $emitted = null;
            $this->emitter->on('test.pending', function ($test) use (&$emitted){
                $emitted = $test;
            });

            $test = new Test('spec', function() {});
            $test->setPending(true);
            $this->result->pendTest($test);
            assert($emitted === $test, 'should have emitted spec:pending event');
        });
    });
});
