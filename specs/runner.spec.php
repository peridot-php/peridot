<?php
describe("Spec", function() {
    it("should have a passing spec", function() {
    });

    it("should have a failing spec", function() {
        throw new Exception("failure");
    });
});
