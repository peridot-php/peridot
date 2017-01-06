<?php

use Peridot\AssertException;

describe('AssertException', function () {
    context('::handle()', function () {
        it('should throw an exception', function () {
            $exception = null;
            try {
                AssertException::handle('/path/to/file', 111, '', 'You done goofed.');
            } catch (AssertException $e) {
                $exception = $e;
            }
            assert(!is_null($exception), 'exception should have been thrown');
            assert($exception->getFile() === '/path/to/file', 'exception should have the correct filename');
            assert($exception->getLine() === 111, 'exception should have the correct line number');
            assert($exception->getMessage() === 'You done goofed.', 'exception should have the correct message');
            assert($exception->getTrace() === [], 'exception should have an empty trace');
        });

        it('should support assertions with an expression', function () {
            $exception = null;
            try {
                AssertException::handle('/path/to/file', 111, 'expression', 'You done goofed.');
            } catch (AssertException $e) {
                $exception = $e;
            }
            assert($exception->getMessage() === 'expression You done goofed.', 'exception should have the correct message');
        });
    });
});
