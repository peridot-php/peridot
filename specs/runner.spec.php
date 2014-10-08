<?php
use Evenement\EventEmitter;
use Peridot\Configuration;
use Peridot\Core\Spec;
use Peridot\Core\Suite;
use Peridot\Runner\Runner;
use Peridot\Core\SpecResult;
use Peridot\Runner\SuiteLoader;

describe("Runner", function() {

    beforeEach(function() {
        $this->result = new SpecResult(new EventEmitter());
        $this->loader = new SuiteLoader('*.spec.php');
    });

    context("running a single suite", function() {
        it("should run a given suite", function() {
            $suite = new Suite("description", function() {});
            $suite->addSpec(new Spec("should do a thing", function() {}));
            $suite->addSpec(new Spec("should fail a thing", function() { throw new \Exception("Fail");}));

            $runner = new Runner($suite, new Configuration(), new EventEmitter());
            $runner->run($this->result);
            assert('2 run, 1 failed' == $this->result->getSummary(), 'result summary should show 2/1');
        });
    });

    context("running a suite with children", function() {
        it("should run a suite with children", function() {
            $parent = new Suite("description 1", function() {});
            $parent->addSpec(new Spec("should do a thing", function() {}));
            $child = new Suite("description 2", function() {});
            $child->addSpec(new Spec("should fail a thing", function() { throw new \Exception("Fail");}));
            $grandchild = new Suite("description 3", function() {});
            $grandchild->addSpec(new Spec("pass a thing", function() { }));
            $parent->addSpec($child);
            $child->addSpec($grandchild);

            $runner = new Runner($parent, new Configuration(), new EventEmitter());
            $runner->run($this->result);
            assert('3 run, 1 failed' == $this->result->getSummary(), 'result summary should show 3/1');
        });
    });

    describe("->run()", function() {

        beforeEach(function() {
            $this->suite = new Suite("runner test suite", function() {});
            $this->passingSpec = new Spec("passing spec", function() {});
            $this->failingSpec = new Spec("failing spec", function() { throw new \Exception("fail"); });
            $this->suite->addSpec($this->passingSpec);
            $this->suite->addSpec($this->failingSpec);
            $this->eventEmitter = new EventEmitter();

            $this->runner = new Runner($this->suite, new Configuration(), $this->eventEmitter);
        });

        it("should emit a start event when the runner starts", function() {
            $emitted = false;
            $this->eventEmitter->on('runner.start', function() use (&$emitted) {
                $emitted = true;
            });
            $this->runner->run(new SpecResult($this->eventEmitter));
            assert($emitted, 'start event should have been emitted');
        });

        it("should emit an end event when the runner ends", function() {
            $emitted = false;
            $this->eventEmitter->on('runner.end', function() use (&$emitted) {
                $emitted = true;
            });
            $result = new SpecResult(new EventEmitter());
            $this->runner->run($result);
            assert($emitted && $result->getSpecCount() > 0, 'end event should have been emitted');
        });

        it("should emit a fail event when a spec fails", function() {
            $emitted = null;
            $exception = null;
            $this->eventEmitter->on('spec.failed', function($spec, $e) use (&$emitted, &$exception) {
                $emitted = $spec;
                $exception = $e;
            });
            $this->runner->run(new SpecResult($this->eventEmitter));
            assert($emitted === $this->failingSpec && !is_null($exception), 'fail event should have been emitted with spec and exception');
        });

        it("should emit a pass event when a spec passes", function() {
            $emitted = null;
            $this->eventEmitter->on('spec.passed', function($spec) use (&$emitted) {
                $emitted = $spec;
            });
            $this->runner->run(new SpecResult($this->eventEmitter));
            assert($emitted === $this->passingSpec, 'pass event should have been emitted');
        });

        it("should emit a pending event when a spec is pending", function() {
            $emitted = null;
            $this->eventEmitter->on('spec.pending', function($spec) use (&$emitted) {
                $emitted = $spec;
            });
            $this->passingSpec->setPending(true);
            $this->runner->run(new SpecResult($this->eventEmitter));
            assert($emitted === $this->passingSpec, 'pending event should have been emitted');
        });

        it("should emit a suite:start event every time a suite starts", function() {
            $child = new Suite("child suite", function() {});
            $grandchild = new Suite("grandchild suite", function() {});
            $child->addSpec($grandchild);
            $this->suite->addSpec($child);
            $count = 0;
            $this->eventEmitter->on('suite.start', function() use (&$count) {
                $count++;
            });
            $this->runner->run(new SpecResult($this->eventEmitter));
            assert(3 == $count, "expected 3 suite:start events to fire");
        });

        it("should emit a suite:end every time a suite ends", function() {
            $child = new Suite("child suite", function() {});
            $grandchild = new Suite("grandchild suite", function() {});
            $child->addSpec($grandchild);
            $this->suite->addSpec($child);
            $count = 0;
            $this->eventEmitter->on('suite.end', function() use (&$count) {
                $count++;
            });
            $this->runner->run(new SpecResult($this->eventEmitter));
            assert(3 == $count, "expected 3 suite:end events to fire");
        });

        context("when configured to bail on failure", function() {

            it("should stop running on failure", function() {
                $suite = new Suite("suite", function() {});
                $passing = new Spec("passing spec", function() {});
                $suite->addSpec($passing);

                $childSuite = new Suite("child suite", function() {});
                $passingChild = new Spec("passing child", function() {});
                $failingChild = new Spec("failing child", function() { throw new Exception("booo"); });
                $passing2Child = new Spec("passing2 child", function() {});
                $childSuite->addSpec($passingChild);
                $childSuite->addSpec($failingChild);
                $childSuite->addSpec($passing2Child);
                $suite->addSpec($childSuite);

                $passing2 = new Spec("passing2 spec", function() {});
                $suite->addSpec($passing2);

                $configuration = new Configuration();
                $configuration->stopOnFailure();
                $suite->setEventEmitter($this->eventEmitter);
                $runner = new Runner($suite, $configuration, $this->eventEmitter);
                $result = new SpecResult($this->eventEmitter);
                $runner->run($result);

                assert($result->getSpecCount() === 3, "spec count should be 3");
            });
        });

        $behavesLikeErrorEmitter = function() {
            $this->suite->addSpec(new Spec("my spec", function() {
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

            $this->runner->run(new SpecResult(new EventEmitter()));
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
