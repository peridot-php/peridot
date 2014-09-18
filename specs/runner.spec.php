<?php
use Peridot\Core\Spec;
use Peridot\Core\Suite;
use Peridot\Runner\Runner;
use Peridot\Core\SpecResult;
use Peridot\Runner\SuiteLoader;

describe("Runner", function() {

    beforeEach(function() {
        $this->result = new SpecResult();
        $this->loader = new SuiteLoader();
    });

    context("running a single suite", function() {
        it("should run a given suite", function() {
            $suite = new Suite("description", function() {});
            $suite->addSpec(new Spec("should do a thing", function() {}));
            $suite->addSpec(new Spec("should fail a thing", function() { throw new \Exception("Fail");}));

            $runner = new Runner($suite);
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

            $runner = new Runner($parent);
            $runner->run($this->result);
            assert('3 run, 1 failed' == $this->result->getSummary(), 'result summary should show 3/1');
        });
    });

    describe("->run()", function() {

        beforeEach(function() {
            $suite = new Suite("runner test suite", function() {});
            $this->passingSpec = new Spec("passing spec", function() {});
            $this->failingSpec = new Spec("failing spec", function() { throw new \Exception("fail"); });
            $suite->addSpec($this->passingSpec);
            $suite->addSpec($this->failingSpec);

            $this->runner = new Runner($suite);
        });

        it("should emit a start event when the runner starts", function() {
            $emitted = false;
            $this->runner->on('start', function() use (&$emitted) {
                $emitted = true;
            });
            $this->runner->run(new SpecResult());
            assert($emitted, 'start event should have been emitted');
        });

        it("should emit an end event when the runner ends", function() {
            $emitted = false;
            $this->runner->on('end', function() use (&$emitted) {
                $emitted = true;
            });
            $result = new SpecResult();
            $this->runner->run($result);
            assert($emitted && $result->getSpecCount() > 0, 'end event should have been emitted');
        });

        it("should emit a fail event when a spec fails", function() {
            $emitted = null;
            $this->runner->on('fail', function($spec) use (&$emitted) {
                $emitted = $spec;
            });
            $this->runner->run(new SpecResult());
            assert($emitted === $this->failingSpec, 'fail event should have been emitted');
        });

        it("should emit a pass event when a spec passes", function() {
            $emitted = null;
            $this->runner->on('pass', function($spec) use (&$emitted) {
                $emitted = $spec;
            });
            $this->runner->run(new SpecResult());
            assert($emitted === $this->passingSpec, 'pass event should have been emitted');
        });
    });
});
