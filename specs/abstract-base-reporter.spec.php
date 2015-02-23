<?php
use Evenement\EventEmitter;
use Peridot\Configuration;
use Peridot\Reporter\AbstractBaseReporter;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\ConsoleOutput;
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
                assert($colored == "\033[32mgood\033[39m", "expected color with ansicon enabled");
            });
        });

        context('when in a tty terminal', function() {

            beforeEach(function() {
                $this->reporter = new TtyTestReporter(new Configuration(), new ConsoleOutput(), new EventEmitter());
            });

            afterEach(function() {
                putenv('PERIDOT_TTY');
            });

            it('should set the PERIDOT_TTY environment variable', function() {
                $this->reporter->color('success', 'text');
                assert(getenv('PERIDOT_TTY') !== false, "peridot tty environment variable should have been set");
            });

            it('should write colors if the PERIDOT_TTY environment variable is present', function() {
                putenv('PERIDOT_TTY=1');
                $text = $this->reporter->color('success', 'text');
                assert("\033[32mtext\033[39m" == $text, "colored text should have been written");
            });

            it('should not write colors when output is not a stream output', function () {
                $output = new BufferedOutput();
                $reporter = new TtyTestReporter(new Configuration(), $output, new EventEmitter());
                $text = $reporter->color('success', 'text');
                assert($text == 'text', 'should not have colored text');
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

class TtyTestReporter extends AbstractBaseReporter
{
    public function init()
    {

    }

    protected function isOnWindows()
    {
        return false;
    }
}
