<?php
use Peridot\Runner\Context;
use Peridot\Runner\SuiteLoader;

describe("SuiteLoader", function() {

    beforeEach(function() {
        $this->loader = new SuiteLoader('*.spec.php');
        $this->fixtures = __DIR__ . '/../fixtures';
        $this->context = Context::getInstance();
        $this->file = $this->context->getFile();
    });

    afterEach(function () {
        $this->context->setFile($this->file);
    });

    describe('->getTests()', function() {
        it("should return file paths matching *.spec.php recursively", function() {
            $tests = $this->loader->getTests($this->fixtures);
            assert(count($tests) == 6, "suite loader should have loaded 6 specs");
        });

        it("should return single file if it exists", function() {
            $test = $this->loader->getTests($this->fixtures . '/test.spec.php');
            assert(count($test) == 1, "suite loader should load 1 spec");
        });

        it("should throw exception if path not found", function() {
            $exception = null;
            try {
                $this->loader->getTests('nope');
            } catch (Exception $e) {
                $exception = $e;
            }
            assert(!is_null($exception), "loader should have thrown exception");
        });

        context('when search path contains a trailing slash', function () {
            it('should not include multiple directory separators', function () {
                $tests = $this->loader->getTests($this->fixtures . '/');
                foreach ($tests as $test) {
                    $double = DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR;
                    $pos = strpos($test, $double);
                    assert($pos === false, 'should not have found double directory separators');
                }
            });
        });
    });

    describe('->load()', function() {
        it('should include files matching the pattern', function() {
            $loader = new SuiteLoader('notaspec.php');
            $loader->load($this->fixtures);
            $exists = function_exists('notaspec');
            assert($exists, 'loader should have included file matching glob pattern');
        });

        it('should set the context file path', function() {
            $loader = new SuiteLoader('test2.spec.php');
            $loader->load($this->fixtures);
            $expected = realpath($this->fixtures . '/test2.spec.php');
            $actual = $this->context->getFile();
            assert($actual === $expected, "expected $expected, got $actual");
        });
    });

});
