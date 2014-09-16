<?php
use Peridot\Runner\Runner;
use Peridot\Core\SpecResult;

describe("Runner", function() {

    beforeEach(function() {
        $this->result = new SpecResult();
        $this->runner = new Runner($this->result);
    });

    it("should run a spec in a file", function() {
        $this->runner->runSpec(__DIR__ . '/../fixtures/samplespec.php');
        assert('2 run, 1 failed' == $this->result->getSummary(), 'result summary should show 2/1');
    });

//    it("should run specs with nested suites", function() {
//        $this->runner->runSpec(__DIR__ . '/../fixtures/sample-nested-spec.php');
//        assert('6 run, 2 failed' == $this->result->getSummary(), 'result summary should show 6/2');
//    });
});
