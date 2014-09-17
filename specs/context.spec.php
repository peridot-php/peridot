<?php

describe('SuiteFactory', function() {

    beforeEach(function() {
        $reflection = new ReflectionClass('Peridot\Runner\Context');
        $context = $reflection->newInstanceWithoutConstructor();
        $construct = $reflection->getConstructor();
        $construct->setAccessible(true);
        $construct->invoke($context);
        $this->context = $context;
    });

    it("should be able to set and get current suite", function() {
        $suite = new \Peridot\Core\Suite("description", function() {});
        $this->context->setCurrentSuite($suite);
        assert($suite === $this->context->getCurrentSuite(), "current suite should be same");
    });

});
