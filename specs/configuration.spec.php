<?php
use Peridot\Configuration;

describe('Configuration', function() {

    beforeEach(function() {
        $this->configuration = new Configuration();
    });

    describe('reporter accessors', function() {
        it("should allow getting and setting of a reporter name", function() {
            $name = 'myreporter';
            $this->configuration->setReporter($name);
            assert($name === $this->configuration->getReporter(), "reporter should equal '$name'");
        });
    });

    describe('->setConfigurationFile()', function() {

        it('should check current working directory with file name', function() {
            $file = 'peridot.php';
            $cwd = getcwd();
            $root = dirname(__DIR__);

            chdir($root);
            $this->configuration->setConfigurationFile($file);
            $path = $this->configuration->getConfigurationFile($file);
            chdir($cwd);

            assert(realpath($path) == realpath("$root/$file"), "paths should be equal");
        });

        it('it should throw an exception if the file does not exist', function() {
            $file = 'nope';
            $exception = null;
            try {
                $this->configuration->setConfigurationFile($file);
            } catch (RuntimeException $e) {
                $exception = $e;
            }
            assert(!is_null($exception), "expected exception to be thrown");
        });
    });

    describe('dsl accessors', function() {
        it("should allow getting and setting of a dsl path", function() {
            $path = 'dsl.php';
            $this->configuration->setDsl($path);
            assert($path === $this->configuration->getDsl(), "dsl should equal '$path'");
        });
    });

});
