<?php
use Evenement\EventEmitter;
use Peridot\Core\Test;
use Peridot\Core\TestResult;
use Peridot\Core\Suite;
use Peridot\Test\ItWasRun;

describe("Suite", function() {

    beforeEach(function() {
       $this->eventEmitter = new EventEmitter();
    });

    describe('->run()', function() {
        it("should run multiple specs", function () {
            $suite = new Suite("Suite", function() {});
            $suite->addTest(new ItWasRun("should pass", function () {}));
            $suite->addTest(new ItWasRun('should fail', function () {
                throw new \Exception('woooooo!');
            }));

            $result = new TestResult($this->eventEmitter);
            $suite->setEventEmitter($this->eventEmitter);
            $suite->run($result);
            assert('2 run, 1 failed' == $result->getSummary(), "result summary should show 2/1");
        });

        it("should pass setup functions to specs", function() {
            $suite = new Suite("Suite", function() {});
            $suite->addSetupFunction(function() {
                $this->log = "setup";
            });

            $fn = function() {
                assert($this->log == "setup", "should have setup in log");
            };

            $suite->addTest(new ItWasRun("should have log", $fn));
            $suite->addTest(new ItWasRun("should also have log", $fn));

            $result = new TestResult($this->eventEmitter);
            $suite->setEventEmitter($this->eventEmitter);
            $suite->run($result);
            assert('2 run, 0 failed' == $result->getSummary(), "result summary should show 2/0");
        });

        it("should pass teardown functions to specs", function() {
            $suite = new Suite("Suite", function() {});
            $suite->addTearDownFunction(function() {
                $this->log = "torn";
            });

            $fn = function() {};

            $test1 = new ItWasRun("should have log", $fn);
            $test2 = new ItWasRun("should have log too", $fn);
            $suite->addTest($test1);
            $suite->addTest($test2);

            $result = new TestResult($this->eventEmitter);
            $suite->setEventEmitter($this->eventEmitter);
            $suite->run($result);

            assert('torntorn' == $test1->log() . $test2->log(), "tear down should have run for both specs");
        });

        it("should set pending status on specs if not null", function() {
            $suite = new Suite("Suite", function() {});
            $suite->setPending(true);
            $fn = function() {};

            $test1 = new ItWasRun("should have log", $fn);
            $suite->addTest($test1);

            $result = new TestResult($this->eventEmitter);
            $suite->setEventEmitter($this->eventEmitter);
            $suite->run($result);

            assert($test1->getPending(), "spec should be pending");
        });

        it("should emit a suite:start event", function() {
            $suite = new Suite("Suite", function() {});
            $emitted = null;
            $this->eventEmitter->on('suite.start', function($s) use (&$emitted) {
                $emitted = $s;
            });
            $result = new TestResult($this->eventEmitter);
            $suite->setEventEmitter($this->eventEmitter);
            $suite->run($result);
            assert($suite === $emitted, 'suite start event should have been emitted');
        });

        it("should emit a suite:end event", function() {
            $suite = new Suite("Suite", function() {});
            $emitted = null;
            $this->eventEmitter->on('suite.end', function($s) use (&$emitted) {
                $emitted = $s;
            });
            $result = new TestResult($this->eventEmitter);
            $suite->setEventEmitter($this->eventEmitter);
            $suite->run($result);
            assert($suite === $emitted, 'suite end event should have been emitted');
        });

        it("should stop when a halt event is received", function() {
            $suite = new Suite("halt suite", function() {});
            $passing = new Test("passing spec", function() {});
            $emitter = $this->eventEmitter;
            $halting = new Test("halting spec", function() use ($emitter) {
                $emitter->emit('suite.halt');
            });
            $passing2 = new Test("passing2 spec", function() {});

            $suite->addTest($passing);
            $suite->addTest($halting);
            $suite->addTest($passing2);

            $result = new TestResult($this->eventEmitter);
            $suite->setEventEmitter($this->eventEmitter);
            $suite->run($result);

            assert($result->getTestCount() == 2, "spec count should be 2");
        });
    });

    describe("->addTest()", function() {

        it("should set parent property on child spec", function() {
            $suite = new Suite("test suite", function() {});
            $test = new Test("test spec", function() {});
            $suite->addTest($test);
            assert($test->getParent() === $suite, "added spec should have parent property set");
        });

    });
});
