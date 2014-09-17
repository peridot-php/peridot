<?php
use Peridot\Core\Spec;
use Peridot\Core\Suite;
use Peridot\Runner\Runner;
use Peridot\Core\SpecResult;
use Peridot\Runner\SuiteLoader;

describe("Runner", function() {

    beforeEach(function() {
        $this->result = new SpecResult();
        $this->loader = new SuiteLoader();
    });

    it("should run a given suite", function() {
        $suite = new Suite("description", function() {});
        $suite->addSpec(new Spec("should do a thing", function() {}));
        $suite->addSpec(new Spec("should fail a thing", function() { throw new \Exception("Fail");}));

        $runner = new Runner($suite);
        $runner->run($this->result);
        assert('2 run, 1 failed' == $this->result->getSummary(), 'result summary should show 2/1');
    });
});
