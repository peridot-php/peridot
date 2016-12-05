<?php

use Peridot\Console\ConfigurationReader;
use Peridot\Console\InputDefinition;
use Symfony\Component\Console\Input\ArrayInput;

describe('ConfigurationReader', function() {

    beforeEach(function() {
        $this->argv = array(
            'path' => ['mypath-a', 'mypath-b'],
            '--focus' => '/focus/',
            '--skip' => '/skip/',
            '--grep' => 'mygrep',
            '--no-colors' => true,
            '--bail' => true,
            '--configuration' => __FILE__
        );
        $this->input = new ArrayInput($this->argv, new InputDefinition());
        $this->assert = function($config) {
            assert($config->getPath() === "mypath-a", "path should be mypath-a");
            assert($config->getPaths() === ['mypath-a', 'mypath-b'], "paths should be mypath-a:mypath-b");
            assert($config->getFocusPattern() === '/focus/', 'focus pattern should be /focus/');
            assert($config->getSkipPattern() === '/skip/', 'skip pattern should be /skip/');
            assert($config->getGrep() === "mygrep", "grep should be mygrep");
            assert(!$config->areColorsEnabled(), "colors should be disabled");
            assert($config->shouldStopOnFailure(), "should stop on failure");
            assert($config->getConfigurationFile() === __FILE__, "config should be current file");
        };
    });

    describe("->read()", function() {

        it("should return configuration from InputInterface", function() {
            $reader = new ConfigurationReader($this->input);
            $config = $reader->read();
            call_user_func($this->assert, $config);
        });

        it("should throw an exception if configuration is specified but does not exist", function() {
            $this->argv['--configuration'] = '/path/to/nope.php';
            $input = new ArrayInput($this->argv, new InputDefinition());
            $reader = new ConfigurationReader($input);
            $exception = null;
            try {
                $reader->read();
            } catch (\RuntimeException $e) {
                $exception = $e;
            }
            assert(!is_null($exception), "exception should not be null");
        });

        it('should filter defaulted paths by existence', function() {
            unset($this->argv['path']);
            $definition = new InputDefinition();
            $definition->getArgument('path')->setDefault([__FILE__, __DIR__, 'nonexistent', 'nonexistent-b']);
            $input = new ArrayInput($this->argv, $definition);
            $reader = new ConfigurationReader($input);
            $config = $reader->read();
            assert($config->getPath() === __FILE__, "path should be __FILE__");
            assert($config->getPaths() === [__FILE__, __DIR__], "paths should be [__FILE__, __DIR__]");
        });

        it('should fall back to the working directory if no default paths exist', function() {
            unset($this->argv['path']);
            $definition = new InputDefinition();
            $definition->getArgument('path')->setDefault(['nonexistent-a', 'nonexistent-b']);
            $input = new ArrayInput($this->argv, $definition);
            $reader = new ConfigurationReader($input);
            $config = $reader->read();
            $cwd = getcwd();
            assert($config->getPath() === $cwd, "path should be current file");
            assert($config->getPaths() === [$cwd], "paths should contain current file only");
        });

    });

    describe('::readInput()', function() {
        it('should read input', function() {
            $config = ConfigurationReader::readInput($this->input);
            call_user_func($this->assert, $config);
        });
    });

});
