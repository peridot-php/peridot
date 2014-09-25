<?php
use Peridot\Core\Spec;
use Peridot\Core\SpecResult;
use Peridot\Core\Suite;
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

        it("should add pending results to result", function () {
            $spec = new Spec('shouldnt run', function() {});
            $spec->setPending(true);
            $result = new SpecResult();
            $spec->run($result);
            assert("1 run, 0 failed, 1 pending" == $result->getSummary(), "result summary should have shown 1 pending");
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

        context("when spec is pending", function() {
           it("should not execute", function() {
               $neverRan = true;
               $spec = new Spec('shouldnt run', function() use (&$neverRan) {
                   $neverRan = false;
               });
               $spec->setPending(true);
               $spec->run(new SpecResult());
               assert($neverRan, 'pending spec should not have run');
           });
        });
    });

    describe("->getTitle()", function() {
       it("should return the full text for a spec including parents", function() {
           $root = new Suite("parent", function() {});
           $child = new Suite("nested", function() {});
           $spec = new Spec("should be rad", function() {});
           $child->addSpec($spec);
           $root->addSpec($child);

           assert($spec->getTitle() == "parent nested should be rad", "title should include text from parents");
       });
    });

    describe('->setPending()', function() {
       it('should set the pending status', function() {
           $spec = new Spec("spec", function() {});
           assert(is_null($spec->isPending()), "spec pending should be null by default");
           $spec->setPending(true);
           assert($spec->isPending(), "spec should be pending");
       });
    });
});
