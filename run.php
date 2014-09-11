<?php
require_once 'Spec.php';
require_once 'ItWasRun.php';
require_once 'SpecResult.php';

$specTest = new Spec("was run should run", function() {
    $spec = new ItWasRun("this should run", function() {
        $this->wasRun = true;
    });
    $spec->run();
    assert($spec->wasRun, "spec should have run");
});
$specTest->run();

$setupSpec = new Spec("should run setup functions", function() {
    $spec = new ItWasRun("this should setup", function() {});
    $spec->addSetUpFunction(function() {
        $this->log .= "setUp ";
    });
    $spec->run();
    assert($spec->log == "setUp ", "spec should have been setup");
});
$setupSpec->run();

$teardownSpec = new Spec("should run teardown functions", function() {
    $spec = new ItWasRun("this should teardown", function() {});
    $spec->addTearDownFunction(function() {
        $this->log .= "tearDown ";
    });
    $spec->run();
    assert($spec->log == "tearDown ", "spec should have been torn down");
});
$teardownSpec->run();

$resultSpec = new Spec("should return a result", function () {
    $spec = new ItWasRun("this should return a result", function () {});
    $result = $spec->run();
    assert("1 run, 0 failed" == $result->getSummary(), "result summary should have shown 1 run");
});
$resultSpec->run();

$failedResultSpec = new Spec("should return a failed result", function () {
    $spec = new ItWasRun("this should return a failed result", function () {
        throw new \Exception('blaaargh');
    });
    $result = $spec->run();
    assert("1 run, 1 failed" == $result->getSummary(), "result summary should have shown 1 failed");
});
$failedResultSpec->run();

