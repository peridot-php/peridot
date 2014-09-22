<?php
use Peridot\Core\SpecResult;
use Peridot\Test\ItWasRun;

describe("Spec", function() {

    describe('->run()', function() {
        it("should run", function() {
            $spec = new ItWasRun("this should run", function() {
                $this->wasRun = true;
            });
            $spec->run(new SpecResult());
            assert($spec->wasRun, "spec should have run");
        });

        it("should run setup functions", function() {
            $spec = new ItWasRun("this should setup", function() {});
            $spec->addSetUpFunction(function() {
                $this->log .= "setUp ";
            });
            $spec->run(new SpecResult());
            assert($spec->log == "setUp ", "spec should have been setup");
        });

        it("should run teardown functions", function() {
            $spec = new ItWasRun("this should teardown", function() {});
            $spec->addTearDownFunction(function() {
                $this->log .= "tearDown ";
            });
            $spec->run(new SpecResult());
            assert($spec->log == "tearDown ", "spec should have been torn down");
        });

        it("should modify a passed in result", function () {
            $spec = new ItWasRun("this should return a result", function () {});
            $result = new SpecResult();
            $spec->run($result);
            assert("1 run, 0 failed" == $result->getSummary(), "result summary should have shown 1 run");
        });

        it("should add failed results to result", function () {
            $spec = new ItWasRun("this should return a failed result", function () {
                throw new \Exception('blaaargh');
            });
            $result = new SpecResult();
            $spec->run($result);
            assert("1 run, 1 failed" == $result->getSummary(), "result summary should have shown 1 failed");
        });
    });

});
