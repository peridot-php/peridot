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

    describe('->getConfigurationFile()', function() {
        it('should check current working directory with file name', function() {
            $file = 'peridot.php';
            $cwd = getcwd();
            $root = dirname(__DIR__);

            chdir($root);
            $this->configuration->setConfigurationFile($file);
            $path = $this->configuration->getConfigurationFile($file);
            chdir($cwd);

            assert($path == $root . DIRECTORY_SEPARATOR . $file, "paths should be equal");
        });

        it('should return supplied value if relative file does not exist', function() {
            $file = 'nope.php';
            $this->configuration->setConfigurationFile($file);
            assert($file == $this->configuration->getConfigurationFile(), "path should be value given");
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
