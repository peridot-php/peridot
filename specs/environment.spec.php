<?php
use Evenement\EventEmitter;
use Peridot\Console\Environment;
use Peridot\Console\InputDefinition;

describe('Environment', function() {
    beforeEach(function() {
        $this->definition = new InputDefinition();
        $this->emitter = new EventEmitter();
        $configPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'config.php';
        $this->environment = new Environment($this->definition, $this->emitter, array(
            'c' => $configPath,
            'configuration' => $configPath
        ));
    });

    describe('->getDefinition()', function() {
        it('should return the input definition', function() {
            $definition = $this->environment->getDefinition();
            assert($definition === $this->definition, 'defintion should have been returned');
        });
    });

    describe('->load()', function() {
        it('should return true when it includes the config file', function() {
            assert($this->environment->load('somedefault.php'), "load should return true on success");
        });

        it('should return false when it cant include the config file', function() {
            $environment = new Environment($this->definition, $this->emitter, ['c' => 'nope.php']);
            assert($environment->load('somedefault.php') == false, "load should return false on failure");
        });
    });
});