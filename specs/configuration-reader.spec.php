<?php

use Peridot\Console\ConfigurationReader;
use Peridot\Console\InputDefinition;
use Symfony\Component\Console\Input\ArrayInput;

describe('ConfigurationReader', function() {

    beforeEach(function() {
        $this->definition = array(
            'path' => 'mypath',
            '--focus' => '/focus/',
            '--skip' => '/skip/',
            '--grep' => 'mygrep',
            '--no-colors' => true,
            '--bail' => true,
            '--configuration' => __FILE__
        );
        $this->input = new ArrayInput($this->definition, new InputDefinition());
        $this->assert = function($config) {
            assert($config->getPath() == "mypath", "path should be mypath");
            assert($config->getFocusPattern() == '/focus/', 'focus pattern should be /focus/');
            assert($config->getSkipPattern() == '/skip/', 'skip pattern should be /skip/');
            assert($config->getGrep() == "mygrep", "grep should be mygrep");
            assert(!$config->areColorsEnabled(), "colors should be disabled");
            assert($config->shouldStopOnFailure(), "should stop on failure");
            assert($config->getConfigurationFile() == __FILE__, "config should be current file");
        };
    });

    describe("->read()", function() {

        it("should return configuration from InputInterface", function() {
            $reader = new ConfigurationReader($this->input);
            $config = $reader->read();
            call_user_func($this->assert, $config);
        });

        it("should throw an exception if configuration is specified but does not exist", function() {
            $this->definition['--configuration'] = '/path/to/nope.php';
            $input = new ArrayInput($this->definition, new InputDefinition());
            $reader = new ConfigurationReader($input);
            $exception = null;
            try {
                $reader->read();
            } catch (\RuntimeException $e) {
                $exception = $e;
            }
            assert(!is_null($exception), "exception should not be null");
        });

    });

    describe('::readInput()', function() {
        it('should read input', function() {
            $config = ConfigurationReader::readInput($this->input);
            call_user_func($this->assert, $config);
        });
    });

});
