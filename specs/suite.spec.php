<?php
use Evenement\EventEmitter;
use Peridot\Core\Spec;
use Peridot\Core\SpecResult;
use Peridot\Core\Suite;
use Peridot\Test\ItWasRun;

describe("Suite", function() {

    beforeEach(function() {
       $this->eventEmitter = new EventEmitter();
    });

    describe('->run()', function() {
        it("should run multiple specs", function () {
            $suite = new Suite("Suite", function() {});
            $suite->addSpec(new ItWasRun("should pass", function () {}));
            $suite->addSpec(new ItWasRun('should fail', function () {
                throw new \Exception('woooooo!');
            }));

            $result = new SpecResult($this->eventEmitter);
            $suite->setEventEmitter($this->eventEmitter);
            $suite->run($result);
            assert('2 run, 1 failed' == $result->getSummary(), "result summary should show 2/1");
        });

        it("should pass setup functions to specs", function() {
            $suite = new Suite("Suite", function() {});
            $suite->addSetUpFunction(function() {
                $this->log = "setup";
            });

            $fn = function() {
                assert($this->log == "setup", "should have setup in log");
            };

            $suite->addSpec(new ItWasRun("should have log", $fn));
            $suite->addSpec(new ItWasRun("should also have log", $fn));

            $result = new SpecResult($this->eventEmitter);
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

            $spec1 = new ItWasRun("should have log", $fn);
            $spec2 = new ItWasRun("should have log too", $fn);
            $suite->addSpec($spec1);
            $suite->addSpec($spec2);

            $result = new SpecResult($this->eventEmitter);
            $suite->setEventEmitter($this->eventEmitter);
            $suite->run($result);

            assert('torntorn' == $spec1->log() . $spec2->log(), "tear down should have run for both specs");
        });

        it("should set pending status on specs if not null", function() {
            $suite = new Suite("Suite", function() {});
            $suite->setPending(true);
            $fn = function() {};

            $spec1 = new ItWasRun("should have log", $fn);
            $suite->addSpec($spec1);

            $result = new SpecResult($this->eventEmitter);
            $suite->setEventEmitter($this->eventEmitter);
            $suite->run($result);

            assert($spec1->getPending(), "spec should be pending");
        });

        it("should emit a suite:start event", function() {
            $suite = new Suite("Suite", function() {});
            $emitted = null;
            $this->eventEmitter->on('suite.start', function($s) use (&$emitted) {
                $emitted = $s;
            });
            $result = new SpecResult($this->eventEmitter);
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
            $result = new SpecResult($this->eventEmitter);
            $suite->setEventEmitter($this->eventEmitter);
            $suite->run($result);
            assert($suite === $emitted, 'suite end event should have been emitted');
        });

        it("should stop when a halt event is received", function() {
            $suite = new Suite("halt suite", function() {});
            $passing = new Spec("passing spec", function() {});
            $emitter = $this->eventEmitter;
            $halting = new Spec("halting spec", function() use ($emitter) {
                $emitter->emit('suite.halt');
            });
            $passing2 = new Spec("passing2 spec", function() {});

            $suite->addSpec($passing);
            $suite->addSpec($halting);
            $suite->addSpec($passing2);

            $result = new SpecResult($this->eventEmitter);
            $suite->setEventEmitter($this->eventEmitter);
            $suite->run($result);

            assert($result->getSpecCount() == 2, "spec count should be 2");
        });
    });

    describe("->addSpec()", function() {

        it("should set parent property on child spec", function() {
            $suite = new Suite("test suite", function() {});
            $spec = new Spec("test spec", function() {});
            $suite->addSpec($spec);
            assert($spec->getParent() === $suite, "added spec should have parent property set");
        });

    });
});
