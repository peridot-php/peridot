<?php

describe('Suite with focused sub-suites', function() {
    it('should not run a normal test', function () {
        throw new RuntimeException();
    });

    xit('should not run a pending test', function () {
        throw new RuntimeException();
    });

    describe('a normal suite', function () {
        it('should not be run', function () {
            throw new RuntimeException();
        });
    });

    xdescribe('a pending suite', function () {
        it('should not be run', function () {
            throw new RuntimeException();
        });
    });

    fit('should run a focused test', function () {});

    fdescribe('a focused suite', function () {
        it('should run each test', function () {});
        it('should run each test', function () {});

        xit('should allow pending tests', function () {
            throw new RuntimeException();
        });

        xdescribe('a nested pending suite', function () {
            it('should have pending child tests', function () {
                throw new RuntimeException();
            });
        });
    });

    fdescribe('a focused suite with nested focused tests', function () {
        it('should not run unfocused tests', function () {
            throw new RuntimeException();
        });

        fit('should run focused tests', function () {});
    });

    describe('an unfocused suite with nested focused tests', function () {
        it('should not run unfocused tests', function () {
            throw new RuntimeException();
        });

        fit('should run focused tests', function () {});
    });

    xdescribe('a pending suite with nested focused tests', function () {
        it('should not run unfocused child tests', function () {
            throw new RuntimeException();
        });

        fit('should mark focused child tests as pending', function () {
            throw new RuntimeException();
        });
    });
});
