<?php

use Peridot\Core\Spec;
use Peridot\Core\SpecResult;
use Peridot\Test\ItWasRun;

describe("Spec", function() {
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

    it('should run tear down functions even if spec fails', function () {
        $spec = new Spec('failing spec with tear downs', function() {
            throw new Exception('fail');
        });
        $spec->addTearDownFunction(function() {
            $this->log = 'tearing down';
        });
        $spec->run(new SpecResult());;
        assert($spec->log == 'tearing down', 'spec should have been torn down after failure');
    });

    it('should run tear down functions even if setup fails', function () {
        $spec = new Spec('spec', function() {});
        $spec->addSetUpFunction(function() {
            throw new Exception('set up failure');
        });
        $spec->addTearDownFunction(function() {
            $this->log = 'tearing down';
        });
        $spec->run(new SpecResult());;
        assert($spec->log == 'tearing down', 'spec should have been torn down after failure');
    });

    it('should continue if tear down fails', function () {
        $spec = new Spec('spec', function() {});
        $spec->addTearDownFunction(function() {
            throw new Exception('tear down failure');
        });

        $result = new SpecResult();
        $spec->run($result);;
        assert("1 run, 0 failed" == $result->getSummary(), "result summary should have shown 1 run");
    });

    it('should skip test if set up fails', function () {
        $spec = new Spec('spec is skipped', function() {
            $this->log = 'testing';
        });
        $spec->addSetUpFunction(function() {
            throw new Exception('set up failure');
        });

        $spec->run(new SpecResult());;
        assert(!isset($spec->log), 'test should have been skipped');
    });
});
