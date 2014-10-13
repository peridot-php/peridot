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
            $tests = $parent->getTests();
            assert($tests[0] === $child, "child should have been added to parent");
        });

        it("should allow sibling suites", function() {
            $sibling1 = $this->context->describe('Sibling1 suite', function() {});
            $sibling2 = $this->context->describe('Sibling2 suite', function() {});
            $tests = $this->context->getCurrentSuite()->getTests();
            assert($tests[0] === $sibling1, "sibling1 should have been added to parent");
            assert($tests[1] === $sibling2, "sibling2 should have been added to parent");
        });

        it("should set pending if value is not null", function() {
            $suite = $this->context->describe("desc", function() {}, true);
            assert($suite->getPending(), "suite should be pending");
        });

        it("should ignore pending if value is null", function() {
            $suite = $this->context->describe("desc", function() {});
            assert(is_null($suite->getPending()), "pending status should be null");
        });
    });

    describe("->it()", function() {
        it("should set pending status if value is not null", function() {
            $test = $this->context->it("is a spec", function() {}, true);
            assert($test->getPending(), "pending status should be true");
        });

        it("should ignore pending status if value is null", function() {
            $test = $this->context->it("is a spec", function() {});
            assert(is_null($test->getPending()), "pending status should be null");
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
