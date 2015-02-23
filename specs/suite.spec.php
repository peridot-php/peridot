<?php
use Evenement\EventEmitter;
use Peridot\Core\Test;
use Peridot\Core\TestResult;
use Peridot\Core\Suite;
use Peridot\Core\Scope;
use Peridot\Test\ItWasRun;

describe("Suite", function() {

    beforeEach(function() {
       $this->eventEmitter = new EventEmitter();
    });

    describe('->run()', function() {
        it("should run multiple tests", function () {
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

        it("should pass setup functions to tests", function() {
            $suite = new Suite("Suite", function() {});
            $suite->addSetupFunction(function() {
                $this->log = "setup";
            });

            $fn = function() {
                assert($this->log == "setup", "should have setup in log");
            };

            $suite->addTest(new Test("should have log", $fn));
            $suite->addTest(new Test("should also have log", $fn));

            $result = new TestResult($this->eventEmitter);
            $suite->setEventEmitter($this->eventEmitter);
            $suite->run($result);
            $expected = "2 run, 0 failed";
            $actual = $result->getSummary();
            assert($expected == $actual, "expected $expected, got $actual");
        });

        it('should pass child scopes to tests', function() {
            $suite = new Suite("Suite", function() {});
            $suite->getScope()->peridotAddChildScope(new SuiteScope());
            $test = new Test("this is a test", function() {
                assert($this->getNumber() == 5, "parent scope should be set on test");
            });
            $suite->addTest($test);
            $result = new TestResult($this->eventEmitter);
            $suite->setEventEmitter($this->eventEmitter);
            $suite->run($result);
            assert('1 run, 0 failed' == $result->getSummary(), "result summary should show 1/0");
        });

        it("should pass teardown functions to tests", function() {
            $suite = new Suite("Suite", function() {});
            $suite->addTearDownFunction(function() {
                $this->log = "torn";
            });

            $fn = function() {};

            $test1 = new Test("should have log", $fn);
            $test2 = new Test("should have log too", $fn);
            $suite->addTest($test1);
            $suite->addTest($test2);

            $result = new TestResult($this->eventEmitter);
            $suite->setEventEmitter($this->eventEmitter);
            $suite->run($result);

            assert('torntorn' == $test1->getScope()->log . $test2->getScope()->log, "tear down should have run for both tests");
        });

        it("should set pending status on tests if not null", function() {
            $suite = new Suite("Suite", function() {});
            $suite->setPending(true);
            $fn = function() {};

            $test1 = new ItWasRun("should have log", $fn);
            $suite->addTest($test1);

            $result = new TestResult($this->eventEmitter);
            $suite->setEventEmitter($this->eventEmitter);
            $suite->run($result);

            assert($test1->getPending(), "test should be pending");
        });

        it("should emit a suite.start event", function() {
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

        it("should emit a suite.end event", function() {
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

            assert($result->getTestCount() == 2, "test count should be 2");
        });
    });

    describe("->addTest()", function() {

        it("should set parent property on child test", function() {
            $suite = new Suite("test suite", function() {});
            $test = new Test("test spec", function() {});
            $suite->addTest($test);
            assert($test->getParent() === $suite, "added test should have parent property set");
        });

    });

    describe('->setTests()', function() {
        beforeEach(function() {
            $this->suite = new Suite("test suite", function() {});
            $test = new Test("test", function() {});
            $this->suite->addTest($test);
        });

        it('should set the tests to the passed in value', function() {
            $this->suite->setTests([]);
            $tests = $this->suite->getTests();
            assert(empty($tests), "tests should be empty");
        });
    });

    describe('file accessors', function () {
        it('should allow access to the file property', function () {
            $this->suite->setFile(__FILE__);
            $file = $this->suite->getFile();
            assert($file === __FILE__);
        });
    });

    describe('->define()', function () {
        beforeEach(function () {
            $this->arg = null;
            $that = $this;
            $this->suite = new Suite('argument testing', function($x) use ($that) {
                $that->arg = $x;
            });
            $this->suite->setEventEmitter($this->eventEmitter);
        });

        it('should call the suite definition with definition arguments', function () {
            $this->suite->setDefinitionArguments([1]);
            $this->suite->define();
            assert($this->arg === 1, 'should have passed argument');
        });

        it('should emit a suite.define event', function () {
            $this->eventEmitter->on('suite.define', function ($suite) {
                $suite->setDefinitionArguments([1]);
            });
            $this->suite->define();
            assert($this->arg === 1, 'should have set definition arguments');
        });
    });
});

class SuiteScope extends Scope
{
    public function getNumber()
    {
        return 5;
    }
}
