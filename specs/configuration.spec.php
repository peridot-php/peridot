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
            $this->configuration->setFocusPattern('/focus/');
            $this->configuration->setSkipPattern('/skip/');
            $this->configuration->setGrep('*.test.php');
            $this->configuration->setReporter('reporter');
            $this->configuration->setPath('/tests');
            $this->configuration->disableColors();
            $this->configuration->stopOnFailure();
            $this->configuration->setDsl(__FILE__);
            $this->configuration->setConfigurationFile(__FILE__);

            $focusPattern = getenv('PERIDOT_FOCUS_PATTERN');
            $skipPattern = getenv('PERIDOT_SKIP_PATTERN');
            $grep = getenv('PERIDOT_GREP');
            $reporter = getenv('PERIDOT_REPORTER');
            $path = getenv('PERIDOT_PATH');
            $colors = getenv('PERIDOT_COLORS_ENABLED');
            $stop = getenv('PERIDOT_STOP_ON_FAILURE');
            $dsl = getenv('PERIDOT_DSL');
            $file = getenv('PERIDOT_CONFIGURATION_FILE');

            assert($focusPattern === '/focus/', 'should have set focus pattern env');
            assert($skipPattern === '/skip/', 'should have set skip pattern env');
            assert($grep === '*.test.php', 'should have set grep env');
            assert($reporter === 'reporter', 'should have set reporter env');
            assert($path === '/tests', 'should have set path env');
            assert(!$colors, 'should have set colors env');
            assert($stop, 'should have set stop env');
            assert($dsl === __FILE__, 'should have set dsl env');
            assert($file === __FILE__, 'should have set config file env');
        });
    });

    describe('->setFocusPattern()', function() {
        it('should normalize patterns that are invalid regular expressions', function() {
            $this->configuration->setFocusPattern('certain ~( type of)? test');
            $pattern = $this->configuration->getFocusPattern();

            assert(preg_match($pattern, 'certain ~ test'), 'normalized pattern should still honor PCRE syntax');
            assert(preg_match($pattern, 'certain ~ type of test'), 'normalized pattern should still honor PCRE syntax');
            assert(preg_match($pattern, 'a certain ~ test with extras'), 'normalized pattern should match text with prefixes and suffixes');
            assert(!preg_match($pattern, 'an uncertain ~ test'), 'normalized pattern should not match text without word boundaries');
        });

        it('should fall back to plaintext matching for regular expressions that cannot be salvaged', function() {
            $this->configuration->setFocusPattern('lol(wat');
            $pattern = $this->configuration->getFocusPattern();

            assert(preg_match($pattern, 'lmao lol(wat huh'), 'normalized pattern should match text with prefixes and suffixes');
            assert(!preg_match($pattern, 'lolol(wat'), 'normalized pattern should not match text without word boundaries');
        });
    });

    describe('->setSkipPattern()', function() {
        it('should normalize patterns that are invalid regular expressions', function() {
            $this->configuration->setSkipPattern('certain ~( type of)? test');
            $pattern = $this->configuration->getSkipPattern();

            assert(preg_match($pattern, 'certain ~ test'), 'normalized pattern should still honor PCRE syntax');
            assert(preg_match($pattern, 'certain ~ type of test'), 'normalized pattern should still honor PCRE syntax');
            assert(preg_match($pattern, 'a certain ~ test with extras'), 'normalized pattern should match text with prefixes and suffixes');
            assert(!preg_match($pattern, 'an uncertain ~ test'), 'normalized pattern should not match text without word boundaries');
        });

        it('should fall back to plaintext matching for regular expressions that cannot be salvaged', function() {
            $this->configuration->setSkipPattern('lol(wat');
            $pattern = $this->configuration->getSkipPattern();

            assert(preg_match($pattern, 'lmao lol(wat huh'), 'normalized pattern should match text with prefixes and suffixes');
            assert(!preg_match($pattern, 'lolol(wat'), 'normalized pattern should not match text without word boundaries');
        });
    });

    describe('->setReporter()', function() {
        it('should set both reporter and reporters', function() {
            $this->configuration->setReporter('reporter-a');

            assert($this->configuration->getReporter() === 'reporter-a', 'should have set reporter');
            assert($this->configuration->getReporters() === ['reporter-a'], 'should have set reporters');
        });
    });

    describe('->setReporters()', function() {
        it('should set both reporter and reporters', function() {
            $this->configuration->setReporters(['reporter-a', 'reporter-b']);

            assert($this->configuration->getReporter() === 'reporter-a', 'should have set reporter');
            assert($this->configuration->getReporters() === ['reporter-a', 'reporter-b'], 'should have set reporters');
        });

        it('should disallow setting an empty reporters array', function() {
            $exception = null;
            try {
                $this->configuration->setReporters([]);
            } catch (InvalidArgumentException $e) {
                $exception = $e;
            }
            assert(!is_null($exception), 'expected exception to be thrown');
        });
    });

    describe('->enableColorsExplicit()', function() {
        it('should enable colors when explicit is set', function() {
            $this->configuration->enableColorsExplicit();
            $this->configuration->disableColors();

            assert(getenv('PERIDOT_COLORS_ENABLED'), 'should have set colors env');
            assert($this->configuration->areColorsEnabled(), 'should have set configuration value');
        });
    });

});
