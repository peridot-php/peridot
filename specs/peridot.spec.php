<?php
/**
 * A simple assertion to test modules are
 * working together as intended
 */
describe('A spec', function () {
    it('should support the truth', function () {
        assert(true);
    });

    it('can pend when omitting a function');

    it('can pend when calling a behavior method', function () {
        $this->pend();
    });

    it('can fail itself', function () {
        $ex = null;
        try {
            $this->fail('failure!!!');
        } catch (Exception $e) {
            $ex = $e;
        }
        assert($ex->getMessage() === 'failure!!!');
    });
});

describe('pending suite');
