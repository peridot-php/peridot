<?php
describe("Spec", function() {

    $this->value = "hello";

    it("should have value", function() {
        assert($this->value == "hello", "there should be value");
    });

    it("should have a passing spec", function() {
    });

    it("should have a failing spec", function() {
        throw new Exception("failure");
    });

    it("should be pending");
});
