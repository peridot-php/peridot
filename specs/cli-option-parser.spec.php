<?php
use Peridot\Console\CliOptionParser;

describe('CliOptionParser', function() {
    beforeEach(function() {
        $this->search = ['-c', '--configuration'];
        $this->args = ["test.php", "-c", "peridot.php", "another", "thing", '--configuration', 'file.php'];
        $this->parser = new CliOptionParser($this->search, $this->args);
    });

    describe('->parse()', function() {
        it('should return values associated with search', function() {
            $values = $this->parser->parse();
            $c = $values['c'];
            $configuration = $values['configuration'];
            assert($c == 'peridot.php', "expected c to equal peridot.php");
            assert($configuration == "file.php", "expected configuration to equal file.php");
        });

        it('should return an empty array if no matches', function() {
            $args = ['test.php', 'ham', 'sandwich'];
            $parser = new CliOptionParser($this->search, $args);
            $parsed = $parser->parse();
            assert(empty($parsed), "empty array should be returned");
        });
    });
});