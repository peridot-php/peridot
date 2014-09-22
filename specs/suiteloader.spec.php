<?php
use Peridot\Runner\SuiteLoader;

describe("SuiteLoader", function() {

    beforeEach(function() {
       $this->loader = new SuiteLoader();
       $this->fixtures = __DIR__ . '/../fixtures';
    });

    describe('->getSpecs()', function() {
        it("should return file paths matching *.spec.php recursively", function() {
            $specs = $this->loader->getSpecs($this->fixtures);
            assert(count($specs) == 4, "suite loader should have loaded 4 specs");
        });

        it("should return single file if it exists", function() {
            $spec = $this->loader->getSpecs($this->fixtures . '/test.spec.php');
            assert(count($spec) == 1, "suite loader should load 1 spec");
        });

        it("should throw exception if path not found", function() {
            $exception = null;
            try {
                $this->loader->getSpecs('nope');
            } catch (Exception $e) {
                $exception = $e;
            }
            assert(!is_null($exception), "loader should have thrown exception");
        });
    });

});
