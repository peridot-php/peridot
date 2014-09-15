<?php
use Peridot\Runner\Runner;
use Peridot\Core\SpecResult;

describe("Runner", function() {
    it("should run a spec in a file", function() {
        $result = new SpecResult();
        $runner = new Runner($result);
        $runner->runSpec(__DIR__ . '/sample.spec.php');
        assert('2 run, 1 failed' == $result->getSummary(), 'result summary should show 2/1');
    });
});
