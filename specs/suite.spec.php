<?php
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
});
