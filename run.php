<?php
include_once 'ItWasRun.php';

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
