<?php
use Peridot\Runner\SuiteLoader;

describe("SuiteLoader", function() {

    beforeEach(function() {
       $this->loader = new SuiteLoader('*.spec.php');
       $this->fixtures = __DIR__ . '/../fixtures';
    });

    describe('->getTests()', function() {
        it("should return file paths matching *.spec.php recursively", function() {
            $tests = $this->loader->getTests($this->fixtures);
            assert(count($tests) == 4, "suite loader should have loaded 4 specs");
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
    });

    describe('->load()', function() {
        it('should include files matching the pattern', function() {
            $loader = new SuiteLoader('notaspec.php');
            $loader->load($this->fixtures);
            $exists = function_exists('notaspec');
            assert($exists, 'loader should have included file matching glob pattern');
        });
    });

});
