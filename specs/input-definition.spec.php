<?php
use Peridot\Console\InputDefinition;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

describe('InputDefinition', function() {
    beforeEach(function() {
        $this->definition = new InputDefinition();
    });

    describe('->option()', function() {
        it('should add an option to the definition', function() {
            $this->definition->option('myopt','-m', InputOption::VALUE_NONE, 'an opt');
            assert(!is_null($this->definition->getOption('myopt')), 'option() should register option');
        });
    });
});
