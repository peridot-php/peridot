<?php
use Peridot\Core\Spec;
use Peridot\Core\SpecResult;
use Peridot\Core\Suite;

describe("Suite", function() {
    it("should run multiple specs", function () {
        $suite = new Suite("Suite");
        $suite->add(new ItWasRun("should pass", function () {}));
        $suite->add(new ItWasRun('should fail', function () {
            throw new \Exception('woooooo!');
        }));

        $result = new SpecResult();
        $suite->run($result);
        assert('2 run, 1 failed' == $result->getSummary(), "result summary should have show 2/1");
    });
});
