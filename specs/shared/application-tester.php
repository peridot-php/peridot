<?php
use Evenement\EventEmitter;
use Peridot\Configuration;
use Peridot\Console\Application;
use Peridot\Console\Command;
use Peridot\Console\Environment;
use Peridot\Console\InputDefinition;
use Peridot\Core\Suite;
use Peridot\Reporter\ReporterFactory;
use Peridot\Runner\Runner;
use Symfony\Component\Console\Output\BufferedOutput;

beforeEach(function() {
    $this->configuration = new Configuration();
    $this->configuration->setGrep('*.stub.php');
    $this->configuration->setPath(__FILE__);

    $this->emitter = new EventEmitter();
    $suite = new Suite("suite", function() {});
    $this->runner = new Runner($suite, $this->configuration, $this->emitter);
    $this->output = new BufferedOutput();
    $this->factory = new ReporterFactory($this->configuration, $this->output, $this->emitter);
    $this->definition = new InputDefinition();

    $this->configPath = __DIR__  . '/../../fixtures/samplespec.php';
    $this->environment = new Environment($this->definition, $this->emitter, ['c' => $this->configPath]);
    $this->application = new Application($this->environment);

    $this->command = new Command($this->runner, $this->configuration, $this->factory, $this->emitter);
    $this->command->setApplication($this->application);
});
