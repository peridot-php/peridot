<?php
use Evenement\EventEmitter;
use Peridot\Core\Test;
use Peridot\Core\TestResult;
use Peridot\Core\Suite;
use Peridot\Test\ItWasRun;

describe("Test", function() {

    context("when constructed with null definition", function() {
        it("it should default to a pending state", function() {
            $test = new Test("it should be pending");
            assert($test->getPending(), "test should be pending if definition is null");
        });
    });

    describe('->run()', function() {
        it("should run", function() {
            $test = new ItWasRun("this should run", function() {
                $this->wasRun = true;
            });
            $test->run(new TestResult(new EventEmitter()));
            assert($test->wasRun(), "spec should have run");
        });

        it("should run setup functions", function() {
            $test = new ItWasRun("this should setup", function() {});
            $test->addSetupFunction(function() {
                $this->log .= "setUp ";
            });
            $test->run(new TestResult(new EventEmitter()));
            assert($test->log() == "setUp ", "spec should have been setup");
        });

        it("should run teardown functions", function() {
            $test = new ItWasRun("this should teardown", function() {});
            $test->addTearDownFunction(function() {
                $this->log .= "tearDown ";
            });
            $test->run(new TestResult(new EventEmitter()));
            assert($test->log() == "tearDown ", "spec should have been torn down");
        });

        it("should modify a passed in result", function () {
            $test = new ItWasRun("this should return a result", function () {});
            $result = new TestResult(new EventEmitter());
            $test->run($result);
            assert("1 run, 0 failed" == $result->getSummary(), "result summary should have shown 1 run");
        });

        it("should add failed results to result", function () {
            $test = new ItWasRun("this should return a failed result", function () {
                throw new \Exception('blaaargh');
            });
            $result = new TestResult(new EventEmitter());
            $test->run($result);
            assert("1 run, 1 failed" == $result->getSummary(), "result summary should have shown 1 failed");
        });

        it("should add pending results to result", function () {
            $test = new Test('shouldnt run', function() {});
            $test->setPending(true);
            $result = new TestResult(new EventEmitter());
            $test->run($result);
            assert("1 run, 0 failed, 1 pending" == $result->getSummary(), "result summary should have shown 1 pending");
        });

        it('should run tear down functions even if spec fails', function () {
            $test = new Test('failing spec with tear downs', function() {
                throw new Exception('fail');
            });
            $test->addTearDownFunction(function() {
                $this->log = 'tearing down';
            });
            $test->run(new TestResult(new EventEmitter()));;
            assert($test->getScope()->log == 'tearing down', 'spec should have been torn down after failure');
        });

        it('should run tear down functions even if setup fails', function () {
            $test = new Test('spec', function() {});
            $test->addSetupFunction(function() {
                throw new Exception('set up failure');
            });
            $test->addTearDownFunction(function() {
                $this->log = 'tearing down';
            });
            $test->run(new TestResult(new EventEmitter()));
            assert($test->getScope()->log == 'tearing down', 'spec should have been torn down after failure');
        });

        it('should continue if tear down fails', function () {
            $test = new Test('spec', function() {});
            $test->addTearDownFunction(function() {
                throw new Exception('tear down failure');
            });

            $result = new TestResult(new EventEmitter());
            $test->run($result);;
            assert("1 run, 0 failed" == $result->getSummary(), "result summary should have shown 1 run");
        });

        it('should skip test if set up fails', function () {
            $test = new Test('spec is skipped', function() {
                $this->log = 'testing';
            });
            $test->getScope()->log = "";
            $test->addSetupFunction(function() {
                throw new Exception('set up failure');
            });

            $test->run(new TestResult(new EventEmitter()));
            assert(empty($test->getScope()->log), 'test should have been skipped');
        });

        context("when spec is pending", function() {
           it("should not execute", function() {
               $neverRan = true;
               $test = new Test('shouldnt run', function() use (&$neverRan) {
                   $neverRan = false;
               });
               $test->setPending(true);
               $test->run(new TestResult(new EventEmitter()));
               assert($neverRan, 'pending spec should not have run');
           });
        });
    });

    describe("->getTitle()", function() {
       it("should return the full text for a spec including parents", function() {
           $root = new Suite("parent", function() {});
           $child = new Suite("nested", function() {});
           $test = new Test("should be rad", function() {});
           $child->addTest($test);
           $root->addTest($child);

           assert($test->getTitle() == "parent nested should be rad", "title should include text from parents");
       });
    });

    describe('->setPending()', function() {
       it('should set the pending status', function() {
           $test = new Test("spec", function() {});
           assert(is_null($test->getPending()), "spec pending should be null by default");
           $test->setPending(true);
           assert($test->getPending(), "spec should be pending");
       });
    });
});
