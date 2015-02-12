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
            $this->eventEmitter = new EventEmitter();

            $this->runner = new Runner($this->suite, new Configuration(), $this->eventEmitter);
        });

        it("should emit a start event when the runner starts", function() {
            $emitted = false;
            $this->eventEmitter->on('runner.start', function() use (&$emitted) {
                $emitted = true;
            });
            $this->runner->run(new TestResult($this->eventEmitter));
            assert($emitted, 'start event should have been emitted');
        });

        it("should emit an end event with run time when the runner ends", function() {
            $time = null;
            $this->eventEmitter->on('runner.end', function($timeToRun) use (&$time) {
                $time = $timeToRun;
            });
            $result = new TestResult(new EventEmitter());
            $this->runner->run($result);
            assert(is_numeric($time) && $result->getTestCount() > 0, 'end event with a time arg should have been emitted');
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

        $behavesLikeErrorEmitter = function() {
            $this->suite->addTest(new Test("my spec", function() {
                trigger_error("This is a user notice", E_USER_NOTICE);
            }));

            $error = [];
            $this->eventEmitter->on('error', function($errno, $errstr, $errfile, $errline) use (&$error) {
                $error = array(
                    'errno' => $errno,
                    'errstr' => $errstr,
                    'errfile' => $errfile,
                    'errline' => $errline
                );
            });

            $this->runner->run(new TestResult(new EventEmitter()));
            assert($error['errno'] == E_USER_NOTICE, "error event should have passed error constant");
            assert($error['errstr'] == "This is a user notice");
        };

        it("should emit an error event with error information", $behavesLikeErrorEmitter);

        it("should restore a previous error handler", function() use ($behavesLikeErrorEmitter) {
            $handler = function($errno, $errstr, $errfile, $errline) {
                //such errors handled. wow!
            };
            set_error_handler($handler);
            call_user_func(Closure::bind($behavesLikeErrorEmitter, $this, $this));
            $old = set_error_handler(function($n,$s,$f,$l) {});
            assert($handler === $old, "runner should have restored previous handler");
        });
    });
});
