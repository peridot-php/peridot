<?php
use Evenement\EventEmitter;
use Peridot\Core\Test;
use Peridot\Core\TestResult;
use Peridot\Core\Suite;
use Peridot\Core\Scope;
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

        context("when test is pending", function() {
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

        context('when definition arguments have been given', function () {
            it('should pass arguments to test', function () {
                $arg = null;
                $test = new Test('should have args', function ($x) use (&$arg) {
                    $arg = $x;
                });
                $test->setDefinitionArguments([1]);
                $test->run(new TestResult(new EventEmitter()));
                assert($arg === 1, 'should have passed argument to test');
            });
        });

        context('when running setup functions', function() {
            it('should run setup functions in order', function() {
                $test = new Test("test", function() {});
                $log = '';
                $test->addSetupFunction(function() use (&$log) {
                    $log = "thing";
                });
                $test->addSetupFunction(function() use (&$log) {
                    $log = "thing2";
                });
                $test->run(new TestResult(new EventEmitter()));
                $expected = "thing2";
                assert($expected == $log, "expected $expected, got $log");
            });

            it('should run parent setup functions first', function() {
                $parent = new Suite("parent", function() {});
                $log = '';
                $parent->addSetupFunction(function() use (&$log) {
                    $log .= "parent ";
                });
                $child = new Suite("child", function() {});
                $child->addSetupFunction(function() use (&$log) {
                    $log .= "child ";
                });
                $grandchild = new Test("grandchild", function() {});
                $grandchild->addSetupFunction(function() use (&$log) {
                    $log .= "grandchild";
                });
                $parent->addTest($child);
                $child->addTest($grandchild);

                $grandchild->run(new TestResult(new EventEmitter()));

                assert("parent child grandchild" == $log, "setup functions should be run in order");
            });
        });

        context("when running tear down functions", function() {
            it('should run child tear down functions first', function() {
                $parent = new Suite("parent", function() {});
                $log = '';
                $parent->addTearDownFunction(function() use (&$log) {
                    $log .= "parent";
                });
                $child = new Suite("child", function() {});
                $child->addTearDownFunction(function() use (&$log) {
                    $log .= "child ";
                });
                $grandchild = new Test("grandchild", function() {});
                $grandchild->addTearDownFunction(function() use (&$log) {
                    $log .= "grandchild ";
                });
                $parent->addTest($child);
                $child->addTest($grandchild);

                $grandchild->run(new TestResult(new EventEmitter()));

                assert("grandchild child parent" == $log, "tear down functions should be run in order");
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
                $test->run($result);
                $expected = "1 run, 1 failed";
                $actual = $result->getSummary();
                assert($expected == $actual, "expected $expected, got $actual");
            });

            it('should not result in a pass and fail if tear down fails', function() {
                $test = new Test("passing", function() {});
                $test->addTearDownFunction(function() {
                    throw new Exception("failure");
                });
                $emitter = new EventEmitter();
                $count = 0;
                $emitter->on('test.passed', function() use (&$count) {
                    $count++;
                });
                $emitter->on('test.failed', function() use (&$count) {
                    $count++;
                });
                $test->run(new TestResult($emitter));
                assert($count == 1, "should not have emitted a pass and fail event");
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

    describe('->setScope()', function() {
        it('should set the scope of the test', function() {
            $scope = new Scope();
            $test = new Test("spec", function() {});
            $test->setScope($scope);
            assert($scope === $test->getScope(), "setScope should have set scope");
        });
    });

    describe('->setParent()', function() {
        it('should bind the parent scope to the child', function() {
            $scope = new Scope();
            $parent = new Suite("parent", function() {});
            $parent->setScope($scope);
            $test = new Test("child", function() {});
            $test->setParent($parent);
            assert($scope === $test->getScope(), "scope should be parent scope");
        });
    });

    describe('file accessors', function () {
        it('should allow access to the file property', function () {
            $test = new Test('test');
            $test->setFile(__FILE__);
            $file = $test->getFile();
            assert($file === __FILE__);
        });
    });
});
