<?php
use Evenement\EventEmitter;
use Peridot\Configuration;
use Peridot\Core\Test;
use Peridot\Core\Suite;
use Peridot\Runner\Runner;
use Peridot\Core\TestResult;
use Peridot\Runner\SuiteLoader;

describe("Runner", function() {

    beforeEach(function() {
        $this->result = new TestResult(new EventEmitter());
        $this->loader = new SuiteLoader('*.spec.php');
    });

    context("running a single suite", function() {
        it("should run a given suite", function() {
            $suite = new Suite("description", function() {});
            $suite->addTest(new Test("should do a thing", function() {}));
            $suite->addTest(new Test("should fail a thing", function() { throw new \Exception("Fail");}));

            $runner = new Runner($suite, new Configuration(), new EventEmitter());
            $runner->run($this->result);
            assert('2 run, 1 failed' == $this->result->getSummary(), 'result summary should show 2/1');
        });
    });

    context("running a suite with children", function() {
        it("should run a suite with children", function() {
            $parent = new Suite("description 1", function() {});
            $parent->addTest(new Test("should do a thing", function() {}));
            $child = new Suite("description 2", function() {});
            $child->addTest(new Test("should fail a thing", function() { throw new \Exception("Fail");}));
            $grandchild = new Suite("description 3", function() {});
            $grandchild->addTest(new Test("pass a thing", function() { }));
            $parent->addTest($child);
            $child->addTest($grandchild);

            $runner = new Runner($parent, new Configuration(), new EventEmitter());
            $runner->run($this->result);
            assert('3 run, 1 failed' == $this->result->getSummary(), 'result summary should show 3/1');
        });
    });

    describe("->run()", function() {

        beforeEach(function() {
            $this->suite = new Suite("runner test suite", function() {});
            $this->passingTest = new Test("passing spec", function() {});
            $this->failingTest = new Test("failing spec", function() { throw new \Exception("fail"); });
            $this->suite->addTest($this->passingTest);
            $this->suite->addTest($this->failingTest);
            $this->configuration = new Configuration();
            $this->eventEmitter = new EventEmitter();

            $this->runner = new Runner($this->suite, $this->configuration, $this->eventEmitter);
        });

        it('should apply focus patterns if a focus pattern has been set', function() {
            $this->configuration->setFocusPattern('/passing/');
            $count = 0;
            $this->eventEmitter->on('test.start', function() use (&$count) {
                $count++;
            });
            $result = new TestResult($this->eventEmitter);
            $this->runner->run($result);
            assert(1 == $count, 'expected 1 test:start events to fire');
            assert(!$result->isFocusedByDsl(), 'should not be focused by DSL');
        });

        it('should apply focus patterns if a skip pattern has been set', function() {
            $this->configuration->setSkipPattern('/failing/');
            $count = 0;
            $this->eventEmitter->on('test.start', function() use (&$count) {
                $count++;
            });
            $result = new TestResult($this->eventEmitter);
            $this->runner->run($result);
            assert(1 == $count, 'expected 1 test:start events to fire');
            assert(!$result->isFocusedByDsl(), 'should not be focused by DSL');
        });

        it('should apply focus patterns if both focus and skip patterns are set', function() {
            $this->suite->addTest(new Test('another passing spec', function() {}));
            $this->configuration->setFocusPattern('/passing/');
            $this->configuration->setSkipPattern('/another/');
            $count = 0;
            $this->eventEmitter->on('test.start', function() use (&$count) {
                $count++;
            });
            $result = new TestResult($this->eventEmitter);
            $this->runner->run($result);
            assert(1 == $count, 'expected 1 test:start events to fire');
            assert(!$result->isFocusedByDsl(), 'should not be focused by DSL');
        });

        it('should mark the result as focused by DSL where appropriate', function() {
            $this->suite->addTest(new Test('another passing spec', function() {}, true));
            $result = new TestResult($this->eventEmitter);
            $this->runner->run($result);
            assert($result->isFocusedByDsl(), 'should be focused by DSL');
        });

        it("should emit a start event when the runner starts", function() {
            $emitted = false;
            $this->eventEmitter->on('runner.start', function() use (&$emitted) {
                $emitted = true;
            });
            $this->runner->run(new TestResult($this->eventEmitter));
            assert($emitted, 'start event should have been emitted');
        });

        it("should emit an end event with run time and result when the runner ends", function() {
            $time = null;
            $emittedResult = null;
            $this->eventEmitter->on('runner.end', function($timeToRun, $result) use (&$time, &$emittedResult) {
                $time = $timeToRun;
                $emittedResult = $result;
            });
            $result = new TestResult(new EventEmitter());
            $this->runner->run($result);
            assert(is_numeric($time) && $result->getTestCount() > 0, 'end event with a time arg should have been emitted');
            assert($emittedResult === $result, 'end event with a result arg should have been emitted');
        });

        it("should emit a fail event when a spec fails", function() {
            $emitted = null;
            $exception = null;
            $this->eventEmitter->on('test.failed', function($test, $e) use (&$emitted, &$exception) {
                $emitted = $test;
                $exception = $e;
            });
            $this->runner->run(new TestResult($this->eventEmitter));
            assert($emitted === $this->failingTest && !is_null($exception), 'fail event should have been emitted with spec and exception');
        });

        it("should emit a pass event when a spec passes", function() {
            $emitted = null;
            $this->eventEmitter->on('test.passed', function($test) use (&$emitted) {
                $emitted = $test;
            });
            $this->runner->run(new TestResult($this->eventEmitter));
            assert($emitted === $this->passingTest, 'pass event should have been emitted');
        });

        it("should emit a pending event when a spec is pending", function() {
            $emitted = null;
            $this->eventEmitter->on('test.pending', function($test) use (&$emitted) {
                $emitted = $test;
            });
            $this->passingTest->setPending(true);
            $this->runner->run(new TestResult($this->eventEmitter));
            assert($emitted === $this->passingTest, 'pending event should have been emitted');
        });

        it("should emit a suite:start event every time a suite starts", function() {
            $child = new Suite("child suite", function() {});
            $grandchild = new Suite("grandchild suite", function() {});
            $child->addTest($grandchild);
            $this->suite->addTest($child);
            $count = 0;
            $this->eventEmitter->on('suite.start', function() use (&$count) {
                $count++;
            });
            $this->runner->run(new TestResult($this->eventEmitter));
            assert(3 == $count, "expected 3 suite:start events to fire");
        });

        it("should emit a suite:end every time a suite ends", function() {
            $child = new Suite("child suite", function() {});
            $grandchild = new Suite("grandchild suite", function() {});
            $child->addTest($grandchild);
            $this->suite->addTest($child);
            $count = 0;
            $this->eventEmitter->on('suite.end', function() use (&$count) {
                $count++;
            });
            $this->runner->run(new TestResult($this->eventEmitter));
            assert(3 == $count, "expected 3 suite:end events to fire");
        });

        context("when configured to bail on failure", function() {

            it("should stop running on failure", function() {
                $suite = new Suite("suite", function() {});
                $passing = new Test("passing spec", function() {});
                $suite->addTest($passing);

                $childSuite = new Suite("child suite", function() {});
                $passingChild = new Test("passing child", function() {});
                $failingChild = new Test("failing child", function() { throw new Exception("booo"); });
                $passing2Child = new Test("passing2 child", function() {});
                $childSuite->addTest($passingChild);
                $childSuite->addTest($failingChild);
                $childSuite->addTest($passing2Child);
                $suite->addTest($childSuite);

                $passing2 = new Test("passing2 spec", function() {});
                $suite->addTest($passing2);

                $configuration = new Configuration();
                $configuration->stopOnFailure();
                $suite->setEventEmitter($this->eventEmitter);
                $runner = new Runner($suite, $configuration, $this->eventEmitter);
                $result = new TestResult($this->eventEmitter);
                $runner->run($result);

                assert($result->getTestCount() === 3, "spec count should be 3");
            });
        });
    });
});
