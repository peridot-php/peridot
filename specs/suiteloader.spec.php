<?php
use Peridot\Runner\SuiteLoader;

describe("SuiteLoader", function() {

    beforeEach(function() {
       $this->loader = new SuiteLoader();
       $this->fixtures = __DIR__ . '/../fixtures';
    });

    it("should return file paths matching *.spec.php", function() {
        $specs = $this->loader->load($this->fixtures);
        assert(count($specs) == 2, "suite loader should have loaded 2 specs");
    });

});