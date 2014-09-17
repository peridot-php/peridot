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
});
