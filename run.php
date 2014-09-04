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
    $spec->addSetupFunction(function() {
        $this->wasSetup = true;
    });
    $spec->run();
    assert($spec->wasSetup, "spec should have been setup");
});
$setupSpec->run();
