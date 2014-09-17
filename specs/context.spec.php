<?php

use Peridot\Core\Suite;

describe('SuiteFactory', function() {

    beforeEach(function() {
        $reflection = new ReflectionClass('Peridot\Runner\Context');
        $context = $reflection->newInstanceWithoutConstructor();
        $construct = $reflection->getConstructor();
        $construct->setAccessible(true);
        $construct->invoke($context);
        $this->context = $context;
    });

    it("should have a root suite", function() {
        $root = $this->context->getRoot();
        assert($root instanceof Suite, "context should have a root suite");
    });

    it("should return root suite as current by default", function() {
        $root = $this->context->getRoot();
        $current = $this->context->getCurrentSuite();
        assert($root === $current, "root should be current by default");
    });

    it("should be able to set and get current suite", function() {
        $suite = new \Peridot\Core\Suite("description", function() {});
        $this->context->setCurrentSuite($suite);
        assert($suite === $this->context->getCurrentSuite(), "current suite should be same");
    });

});
