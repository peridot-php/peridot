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

    context("when constructed with default parameters", function() {
        it("it should default to an unfocused state", function() {
            $suite = new Suite("Suite", function() {});
            assert(!$suite->isFocused(), "suite should not be focused if focused value is not supplied");
        });
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

        context('when there are focused tests', function() {
            it("should run only the focused tests", function () {
                $suite = new Suite("Suite", function() {});
                $suite->addTest(new ItWasRun("should pass", function () {}, true));
                $suite->addTest(new ItWasRun('should fail', function () {
                    throw new \Exception('woooooo!');
                }, true));
                $suite->addTest(new ItWasRun("should not be run", function () {}));
                $suite->addTest(new ItWasRun("should also not be run", function () {}));

                $result = new TestResult($this->eventEmitter);
                $suite->setEventEmitter($this->eventEmitter);
                $suite->run($result);
                assert('2 run, 1 failed' == $result->getSummary(), "result summary should show 2/1");
            });
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

    describe('->isFocused()', function() {
        context('when explicitly marked as focused', function () {
            beforeEach(function() {
                $this->suite = new Suite('test suite', function() {}, true);
            });

            it('should return true even if nested tests are not focused', function() {
                $test = new Test('test', function() {});
                $this->suite->addTest($test);
                assert($this->suite->isFocused(), 'suite should be focused');
            });
        });

        context('when not explicitly marked as focused', function () {
            beforeEach(function() {
                $this->suite = new Suite('test suite', function() {});
            });

            it('should return false if nested tests are not focused', function() {
                $test = new Test('test', function() {});
                $this->suite->addTest($test);
                assert(!$this->suite->isFocused(), 'suite should not be focused');
            });

            it('should return true if nested tests are focused', function() {
                $test = new Test('test', function() {}, true);
                $this->suite->addTest($test);
                assert($this->suite->isFocused(), 'suite should be focused');
            });

            it('should return true if nested suites are focused', function() {
                $suite = new Suite('nested suite', function() {});
                $test = new Test('test', function() {}, true);
                $suite->addTest($test);
                $this->suite->addTest($suite);
                assert($this->suite->isFocused(), 'suite should be focused');
            });
        });
    });

    describe('->applyFocusPatterns()', function() {
        beforeEach(function () {
            $this->childSuite = new Suite('Child suite', function() {});
            $this->childTestA = new ItWasRun('Test A', function () {});
            $this->childSuite->addTest($this->childTestA);
            $this->childTestB = new ItWasRun('Test B', function () {});
            $this->childSuite->addTest($this->childTestB);
            $this->parentSuite = new Suite('Parent suite', function() {});
            $this->parentTestA = new ItWasRun('Test A', function () {});
            $this->parentSuite->addTest($this->parentTestA);
            $this->parentTestB = new ItWasRun('Test B', function () {});
            $this->parentSuite->addTest($this->parentTestB);
            $this->parentSuite->addTest($this->childSuite);
            $this->suite = new Suite('Grandparent suite', function() {});
            $this->grandparentTestA = new ItWasRun('Test A', function () {});
            $this->suite->addTest($this->grandparentTestA);
            $this->grandparentTestB = new ItWasRun('Test B', function () {});
            $this->suite->addTest($this->grandparentTestB);
            $this->suite->addTest($this->parentSuite);
        });

        it('should apply focus patterns to the suite itself', function() {
            $this->suite->applyFocusPatterns('/Grandparent/');
            assert($this->suite->isFocused(), 'suite should be focused');

            $this->suite->applyFocusPatterns(null, '/Grandparent/');
            assert(!$this->suite->isFocused(), 'suite should not be focused');
        });

        it('should apply focus patterns recursively to all children', function() {
            $this->suite->applyFocusPatterns('/Test A/');
            assert($this->childTestA->isFocused(), 'test should be focused');
            assert(!$this->childTestB->isFocused(), 'test should not be focused');
            assert($this->parentTestA->isFocused(), 'test should be focused');
            assert(!$this->parentTestB->isFocused(), 'test should not be focused');
            assert($this->grandparentTestA->isFocused(), 'test should be focused');
            assert(!$this->grandparentTestB->isFocused(), 'test should not be focused');

            $this->suite->applyFocusPatterns(null, '/Test A/');
            assert(!$this->childTestA->isFocused(), 'test should not be focused');
            assert($this->childTestB->isFocused(), 'test should be focused');
            assert(!$this->parentTestA->isFocused(), 'test should not be focused');
            assert($this->parentTestB->isFocused(), 'test should be focused');
            assert(!$this->grandparentTestA->isFocused(), 'test should not be focused');
            assert($this->grandparentTestB->isFocused(), 'test should be focused');
        });

        it('should apply skip patterns after focus patterns', function() {
            $this->suite->applyFocusPatterns('/Parent suite/', '/Child suite/');
            assert(!$this->childSuite->isFocused(), 'suite should not be focused');
            assert(!$this->childTestA->isFocused(), 'test should not be focused');
            assert(!$this->childTestB->isFocused(), 'test should not be focused');
            assert($this->parentSuite->isFocused(), 'suite should be focused');
            assert($this->parentTestA->isFocused(), 'test should be focused');
            assert($this->parentTestB->isFocused(), 'test should be focused');
            assert($this->suite->isFocused(), 'suite should be focused');
            assert(!$this->grandparentTestA->isFocused(), 'test should not be focused');
            assert(!$this->grandparentTestB->isFocused(), 'test should not be focused');
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
