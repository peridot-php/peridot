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

    $this->emitter = new EventEmitter();
    $suite = new Suite("suite", function() {});
    $this->runner = new Runner($suite, $this->configuration, $this->emitter);
    $this->output = new BufferedOutput();
    $this->factory = new ReporterFactory($this->configuration, $this->output, $this->emitter);
    $this->definition = new InputDefinition();

    $path = __DIR__  . '/../../fixtures/notaspec.php';
    $environment = new Environment($this->definition, $this->emitter, ['c' => $path]);
    $this->application = new Application($environment);

    $this->command = new Command($this->runner, $this->configuration, $this->factory, $this->emitter);
    $this->command->setApplication($this->application);
});
