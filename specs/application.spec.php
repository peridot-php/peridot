<?php
use Peridot\Console\Application;

describe('Application', function() {
    include __DIR__ . '/shared/application-tester.php';

    context('during construction', function() {
        it('should emit peridot.start with environment and self', function() {
            $ref = null;
            $environment = null;
            $this->emitter->on('peridot.start', function($env, $r) use (&$ref, &$environment) {
                $ref = $r;
                $environment = $env;
            });
            $application = new Application($this->environment);
            assert($ref === $application, "application reference should be emitted");
            assert($environment === $this->environment, "environment reference should be emitted");
        });
    });

    describe('->loadDsl()', function() {
        it('should include a file if it exists', function() {
            $this->application->loadDsl(__DIR__ . '/../fixtures/rad.dsl.php');
            assert(function_exists('peridotRadDescribe'), 'dsl should have been included');
        });

        it('should not include the same dsl twice', function() {
            $this->application->loadDsl(__DIR__ . '/../fixtures/rad.dsl2.php');
            $this->application->loadDsl(__DIR__ . '/../fixtures/rad.dsl2.php');
        });
    });

    describe('->getCommandName()', function() {
        it('should return "peridot"', function() {
            assert($this->application->getCommandName() == "peridot", "command name should be peridot");
        });
    });

    describe('->getInput()', function() {
        it('should return an input', function() {
            $input = $this->application->getInput(['foo.php', 'bar']);
            assert(!is_null($input), "getInput should return an input");
        });
    });

    describe('->add()', function() {
        it('should not overwrite a command of the same name', function() {
            $tester = new TestCommand();
            $this->application->add($tester);
            $again = $this->application->add($tester);
            assert($tester === $again, "expected first command instance");
        });
    });
});

class TestCommand extends \Symfony\Component\Console\Command\Command
{
    public function __construct()
    {
        parent::__construct('tester');
    }
}
