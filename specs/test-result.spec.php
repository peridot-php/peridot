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

    describe('->startTest()', function() {
        beforeEach(function() {
            $this->eventEmitter = new EventEmitter();
            $this->result = new TestResult($this->eventEmitter);
        });

        it('should increment the test count', function() {
            $test = new Test('test', function () {});
            $this->result->startTest($test);
            assert(1 === $this->result->getTestCount(), "test count should be 1");
        });

        it('should fire a test.start event', function() {
            $emitted = null;
            $this->eventEmitter->on('test.start', function($test) use (&$emitted) {
                $emitted = $test;
            });
            $test = new Test('some test', function() {});
            $this->result->startTest($test);
            assert($emitted == $test, 'test.start should have emitted a test');
        });

    });

    describe('->endTest()', function() {
        beforeEach(function() {
            $this->eventEmitter = new EventEmitter();
            $this->result = new TestResult($this->eventEmitter);
        });

        it('should fire a test.end event', function() {
            $emitted = null;
            $this->eventEmitter->on('test.end', function($test) use (&$emitted) {
                $emitted = $test;
            });
            $test = new Test('some test', function() {});
            $this->result->endTest($test);
            assert($emitted == $test, 'test.end should have emitted a test');
        });

    });

    describe("->failTest()", function() {
        beforeEach(function() {
            $this->eventEmitter = new EventEmitter();
            $this->result = new TestResult($this->eventEmitter);
        });

        it('should emit a test.failed event', function() {
            $emitted = null;
            $exception = null;
            $this->eventEmitter->on('test.failed', function ($test, $e) use (&$emitted, &$exception){
                $emitted = $test;
                $exception = $e;
            });

            $test = new Test('spec', function() {});
            $e = new \Exception("failure");
            $this->result->failTest($test, $e);
            assert($emitted === $test && $exception != null, 'should have emitted test.failed event with a spec and exception');
       });
    });

    describe("->passTest()", function() {
        beforeEach(function() {
            $this->emitter = new EventEmitter();
            $this->result = new TestResult($this->emitter);
        });

        it('should emit a test.passed event', function() {
            $emitted = null;
            $this->emitter->on('test.passed', function ($test) use (&$emitted){
                $emitted = $test;
            });

            $test = new Test('spec', function() {});
            $this->result->passTest($test);
            assert($emitted === $test, 'should have emitted test.passed event');
        });
    });

    describe("->pendTest()", function() {
        beforeEach(function() {
            $this->emitter = new EventEmitter();
            $this->result = new TestResult($this->emitter);
        });

        it('should emit a test.pending event', function() {
            $emitted = null;
            $this->emitter->on('test.pending', function ($test) use (&$emitted){
                $emitted = $test;
            });

            $test = new Test('spec', function() {});
            $test->setPending(true);
            $this->result->pendTest($test);
            assert($emitted === $test, 'should have emitted test.pending event');
        });
    });

    describe('pending test accessors', function () {
        it('should allow access to the total number of pending tests', function () {
            $result = new TestResult($this->eventEmitter);
            $result->setPendingCount(1);
            assert($result->getPendingCount() === 1, 'should have returned pending count');
        });
    });

    describe('failure accessors', function () {
        it('should allow access to the total number of failures', function () {
            $result = new TestResult($this->eventEmitter);
            $result->setFailureCount(1);
            assert($result->getFailureCount() === 1, 'should have returned failure count');
        });
    });

    describe('test count accessors', function () {
        it('should allow access to the total number of tests', function () {
            $result = new TestResult($this->eventEmitter);
            $result->setTestCount(1);
            assert($result->getTestCount() === 1, 'should have returned test count');
        });
    });

    describe('focused by DSL accessors', function () {
        it('should allow access to the DSL focus state', function () {
            $result = new TestResult($this->eventEmitter);
            $result->setIsFocusedByDsl(true);
            assert($result->isFocusedByDsl(), 'should be focused by DSL');
        });
    });
});
