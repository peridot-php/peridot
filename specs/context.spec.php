<?php

describe('Context', function() {

    beforeEach(function() {
        $reflection = new ReflectionClass('Peridot\Runner\Context');
        $context = $reflection->newInstanceWithoutConstructor();
        $construct = $reflection->getConstructor();
        $construct->setAccessible(true);
        $construct->invoke($context);
        $this->context = $context;
    });

    it("should allow nesting of suites via describe", function() {
        $context = $this->context;
        $child = null;
        $parent = $context->describe('Parent suite', function() use ($context, &$child) {
            $child = $context->describe('Child suite', function() use ($context) {
            });
        });
        $specs = $parent->getSpecs();
        assert($specs[0] === $child, "child should have been added to parent");
    });

    it("should allow sibling suites via describe", function() {
        $sibling1 = $this->context->describe('Sibling1 suite', function() {});
        $sibling2 = $this->context->describe('Sibling2 suite', function() {});
        $specs = $this->context->getCurrentSuite()->getSpecs();
        assert($specs[0] === $sibling1, "sibling1 should have been added to parent");
        assert($specs[1] === $sibling2, "sibling2 should have been added to parent");
    });

});
