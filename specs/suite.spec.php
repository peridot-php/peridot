<?php
use Peridot\Core\Spec;
use Peridot\Core\SpecResult;
use Peridot\Core\Suite;
use Peridot\Test\ItWasRun;

describe("Suite", function() {
    it("should run multiple specs", function () {
        $suite = new Suite("Suite", function() {});
        $suite->addSpec(new ItWasRun("should pass", function () {}));
        $suite->addSpec(new ItWasRun('should fail', function () {
            throw new \Exception('woooooo!');
        }));

        $result = new SpecResult();
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

        $result = new SpecResult();
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

        $result = new SpecResult();
        $suite->run($result);

        assert('torntorn' == $spec1->log . $spec2->log, "tear down should have run for both specs");
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
