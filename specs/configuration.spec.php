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

    describe('setters', function () {
        it('should write corresponding peridot environment variables', function () {
            $this->configuration->setGrep('*.test.php');
            $this->configuration->setReporter('reporter');
            $this->configuration->setPath('/tests');
            $this->configuration->disableColors();
            $this->configuration->stopOnFailure();
            $this->configuration->setDsl(__FILE__);
            $this->configuration->setConfigurationFile(__FILE__);

            $grep = getenv('PERIDOT_GREP');
            $reporter = getenv('PERIDOT_REPORTER');
            $path = getenv('PERIDOT_PATH');
            $colors = getenv('PERIDOT_COLORS_ENABLED');
            $stop = getenv('PERIDOT_STOP_ON_FAILURE');
            $dsl = getenv('PERIDOT_DSL');
            $file = getenv('PERIDOT_CONFIGURATION_FILE');

            assert($grep === '*.test.php', 'should have set grep env');
            assert($reporter === 'reporter', 'should have set reporter env');
            assert($path === '/tests', 'should have set path env');
            assert(!$colors, 'should have set colors env');
            assert($stop, 'should have set stop env');
            assert($dsl === __FILE__, 'should have set dsl env');
            assert($file === __FILE__, 'should have set config file env');
        });
    });

});
