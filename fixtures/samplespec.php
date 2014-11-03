<?php
describe("Spec", function() {

    $this->value = "hello";

    it("should have value", function() {
        assert($this->value == "hello", "there should be value");
    });

    it("should have a passing spec", function() {
    });

    xit("should have a failing spec", function() {
        throw new Exception("failure");
    });

    it("should be pending");

    beforeEach(function() {
       $this->thing = new ArrayObject();
    });

    describe('A nested suite', function() {

        beforeEach(function() {
            $this->thing->append('hello');
        });

        it('should have access to thing', function() {
            assert($this->thing instanceof ArrayObject, "should be array object");
            assert($this->thing[0] == "hello", 'hello should be a thing');
        });
    });
});
