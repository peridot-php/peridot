<?php

describe('SuiteFactory', function() {

    beforeEach(function() {
        $reflection = new ReflectionClass('Peridot\Runner\Context');
        $this->factory = $reflection->newInstanceWithoutConstructor();
    });

    it("should be able to set and get current suite", function() {
        $suite = new \Peridot\Core\Suite("description", function() {});
        $this->factory->setCurrentSuite($suite);
        assert($suite === $this->factory->getCurrentSuite(), "current suite should be same");
    });

});
