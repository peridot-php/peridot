<?php
use Evenement\EventEmitter;
use Peridot\Configuration;
use Peridot\Reporter\AbstractBaseReporter;
use Symfony\Component\Console\Output\NullOutput;

describe('AbstractBaseReporter', function() {

    beforeEach(function() {
        $config = new Configuration();
        $output = new NullOutput();
        $emitter = new EventEmitter();
        $this->reporter = new WindowsTestReporter($config, $output, $emitter);
    });

    describe('->symbol()', function() {
        context('when in a windows environment', function() {
            it('should return ASCII char 251 for check symbol', function() {
                $symbol = $this->reporter->symbol('check');
                assert($symbol == chr(251), "expected ASCII char 251");
            });
        });
    });

    describe('->color()', function() {
        context('when in a windows environment', function() {
            beforeEach(function() {
                $this->ansicon = getenv('ANSICON');
                putenv('ANSICON=1');
            });

            afterEach(function() {
                putenv('ANSICON=' . $this->ansicon);
            });

            it ('should add escape sequences if ansicon is enabled', function() {
                $colored = $this->reporter->color('success', 'good');
                assert($colored == '<fg=green>good</fg=green>', "expected color with ansicon enabled");
            });
        });
    });
});

class WindowsTestReporter extends AbstractBaseReporter
{
    public function init()
    {

    }

    protected function isOnWindows()
    {
        return true;
    }
}
