<?php

use Peridot\Core\SpecResult;
use Peridot\Core\Suite;
use Peridot\Test\ItWasRun;

describe("SpecResult", function() {
    it("should return the number of tests run", function() {
        $result = new SpecResult();
        $suite = new Suite("Suite", function() {});
        $suite->addspec(new ItWasRun("this was run", function () {}));
        $suite->addspec(new ItWasRun("this was also run", function () {}));
        $suite->run($result);
        assert($result->getSpecCount() === 2, "two specs should have run");
    });

    it("should return the number of tests failed", function() {
        $result = new SpecResult();
        $suite = new Suite("Suite", function() {});
        $suite->addspec(new ItWasRun("this was run", function () {}));
        $suite->addspec(new ItWasRun("this was also run", function () {}));
        $suite->addspec(new ItWasRun("this failed", function () {
            throw new Exception('spec failed');
        }));
        $suite->run($result);
        assert($result->getFailureCount() === 1, "one specs should have failed");
    });
});