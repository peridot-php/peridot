<?php
require_once 'Suite.php';
require_once 'Spec.php';
require_once 'ItWasRun.php';
require_once 'SpecResult.php';
require_once 'Runner.php';

$specTest = new Spec("was run should run", function() {
    $spec = new ItWasRun("this should run", function() {
        $this->wasRun = true;
    });
    $spec->run(new SpecResult());
    assert($spec->wasRun, "spec should have run");
});
$specTest->run(new SpecResult());

$setupSpec = new Spec("should run setup functions", function() {
    $spec = new ItWasRun("this should setup", function() {});
    $spec->addSetUpFunction(function() {
        $this->log .= "setUp ";
    });
    $spec->run(new SpecResult());
    assert($spec->log == "setUp ", "spec should have been setup");
});
$setupSpec->run(new SpecResult());

$teardownSpec = new Spec("should run teardown functions", function() {
    $spec = new ItWasRun("this should teardown", function() {});
    $spec->addTearDownFunction(function() {
        $this->log .= "tearDown ";
    });
    $spec->run(new SpecResult());
    assert($spec->log == "tearDown ", "spec should have been torn down");
});
$teardownSpec->run(new SpecResult());

$resultSpec = new Spec("should return a result", function () {
    $spec = new ItWasRun("this should return a result", function () {});
    $result = new SpecResult();
    $spec->run($result);
    assert("1 run, 0 failed" == $result->getSummary(), "result summary should have shown 1 run");
});
$resultSpec->run(new SpecResult());

$failedResultSpec = new Spec("should return a failed result", function () {
    $spec = new ItWasRun("this should return a failed result", function () {
        throw new \Exception('blaaargh');
    });
    $result = new SpecResult();
    $spec->run($result);
    assert("1 run, 1 failed" == $result->getSummary(), "result summary should have shown 1 failed");
});
$failedResultSpec->run(new SpecResult());

$suiteSpec = new Spec("should run a test suite", function () {
    $suite = new Suite("Suite");
    $suite->add(new ItWasRun("should pass", function () {}));
    $suite->add(new ItWasRun('should fail', function () {
        throw new \Exception('woooooo!');
    }));

    $result = new SpecResult();
    $suite->run($result);
    assert('2 run, 1 failed' == $result->getSummary(), "result summary should have show 2/1");
});
$suiteSpec->run(new SpecResult());

$runnerSpec = new Spec("should run a spec in a file", function() {
    $runner = new Runner();
    $runner->runSpec(__DIR__ . '/runner.spec.php');
    $result = $runner->getResult();
    assert('2 run, 1 failed' == $result->getSummary(), 'result summary should show 2/1');
});
$runnerSpec->run(new SpecResult());
