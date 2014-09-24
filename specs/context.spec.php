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

    describe('->describe()', function() {
        it("should allow nesting of suites", function() {
            $context = $this->context;
            $child = null;
            $parent = $context->describe('Parent suite', function() use ($context, &$child) {
                $child = $context->describe('Child suite', function() use ($context) {
                });
            });
            $specs = $parent->getSpecs();
            assert($specs[0] === $child, "child should have been added to parent");
        });

        it("should allow sibling suites", function() {
            $sibling1 = $this->context->describe('Sibling1 suite', function() {});
            $sibling2 = $this->context->describe('Sibling2 suite', function() {});
            $specs = $this->context->getCurrentSuite()->getSpecs();
            assert($specs[0] === $sibling1, "sibling1 should have been added to parent");
            assert($specs[1] === $sibling2, "sibling2 should have been added to parent");
        });
    });

    describe('->beforeEach()', function() {
        it('should register an beforeEach callback on the current suite', function() {
            $before = function() {};
            $this->context->beforeEach($before);
            assert($this->context->getCurrentSuite()->getSetUpFunctions()[0] === $before, "expected beforeEach to register setup function");
        });
    });

    describe('->afterEach()', function() {
       it('should register an afterEach callback on the current suite', function() {
           $after = function() {};
           $this->context->afterEach($after);
           assert($this->context->getCurrentSuite()->getTearDownFunctions()[0] === $after, "expected afterEach to register tear down function");
       });
    });
});
