<?php
describe('Application', function() {
    include __DIR__ . '/shared/application-tester.php';

    describe('->loadDsl()', function() {
        it('should include a file if it exists', function() {
            $this->application->loadDsl(__DIR__ . '/../fixtures/rad.dsl.php');
            assert(function_exists('peridotRadDescribe'), 'dsl should have been included');
        });
    });

    describe('->getCommandName()', function() {
        it('should return "peridot"', function() {
            assert($this->application->getCommandName() == "peridot", "command name should be peridot");
        });
    });

    describe('->getInput()', function() {
        it('should return an input', function() {
            $input = $this->application->getInput();
            assert(!is_null($input), "getInput should return an input");
        });
    });
});
