<?php
describe("Parent Suite", function() {
    it("should have a passing root spec", function() {

    });

    describe("Child Suite", function() {
       it("should have a passing child spec", function() {

       });

       it("should have a failing child spec", function() {
          throw new \Exception("Failure");
       });

       describe("Grandchild Suite", function() {
           it("should have a passing grandchild spec", function() {

           });

           it("should have a failing grandchild spec", function() {
               throw new \Exception("Failure");
           });
       });
    });

    describe("Child Suite 2", function() {
        it("should have a passing child spec", function() {

        });
    });
});
