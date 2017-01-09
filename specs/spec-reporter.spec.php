<?php
use Evenement\EventEmitter;
use Peridot\Configuration;
use Peridot\Core\Test;
use Peridot\Core\TestResult;
use Peridot\Reporter\SpecReporter;
use Symfony\Component\Console\Output\BufferedOutput;

describe('SpecReporter', function() {

    beforeEach(function() {
        $this->configuration = new Configuration();
        $this->output = new BufferedOutput();
        $this->emitter = new EventEmitter();
        $this->reporter = new SpecReporter($this->configuration, $this->output, $this->emitter);
    });

    context('when test.failed is emitted', function() {
        it('should include an error number and the test description', function() {
            $test = new Test("test", function() {});
            $this->emitter->emit('test.failed', [$test, new Exception()]);
            $contents = $this->output->fetch();
            assert(strstr($contents, '1) test') !== false, "error count and test description should be present");
        });
    });

    context('when test.pending is emitted', function() {
        it('should include an error number and the test description', function() {
            $test = new Test("test", function() {});
            $this->emitter->emit('test.pending', [$test]);
            $contents = $this->output->fetch();
            assert(strstr($contents, '- test') !== false, "dash and test description should be present");
        });
    });

    describe('->color()', function() {
        context('when colors are disabled', function() {
            it('should return plain text', function() {
                $this->configuration->disableColors();
                $text = $this->reporter->color('color', 'hello world');
                assert($text == "hello world", "disabled colors should contain color sequences");
            });
        });
    });

    describe('->footer()', function() {
        beforeEach(function(){
            $this->configuration->disableColors();

            $exception = null;
            try {
                $expectedTrace = preg_quote(sprintf('%s:%d', __FILE__, __LINE__ + 1), '~') . '.*';
                new ExceptionThrower($expectedTrace);
            } catch (Exception $e) {
                $exception = $e;
            }
            $this->exception = $exception;
            $this->expectedTrace = '~' . $expectedTrace . '~s';

            $this->emitter->emit('test.passed', [new Test('passing test', function() {})]);
            $this->emitter->emit('test.failed', [new Test('failing test', function() {}), $this->exception]);
            $this->emitter->emit('test.pending', [new Test('pending test', function(){})]);
            $this->reporter->footer();
            $this->contents = $this->output->fetch();
        });

        it('should output success text', function() {
            assert(strstr($this->contents, '1 passing') !== false, 'should contain passing text');
        });

        it('should output time', function() {
            $time = PHP_Timer::secondsToTimeString($this->reporter->getTime());
            assert(strstr($this->contents, $time) !== false, 'should contain time text');
        });

        it('should output failure text', function() {
            assert(strstr($this->contents, '1 failing') !== false, 'should contain failure text');
        });

        it('should output pending count', function() {
            assert(strstr($this->contents, '1 pending') !== false, 'should contain pending text');
        });

        it('should display exception stacks and messages', function() {
            $expectedExceptionMessage = "     ooops" . PHP_EOL . "     nextline";
            assert(strstr($this->contents, $expectedExceptionMessage) !== false, "should include exception message");
            assert(preg_match($this->expectedTrace, $this->contents), 'should include exception stack');
        });
    });

    describe('->warnings()', function() {
        it('should output DSL focused test warnings', function() {
            $this->configuration->disableColors();
            $result = new TestResult($this->emitter);
            $result->setIsFocusedByDsl(true);
            $this->emitter->emit('runner.end', [1.0, $result]);
            $this->reporter->warnings($result);
            $this->contents = $this->output->fetch();
            $expected = 'WARNING: Tests have been focused programmatically.';
            assert(strstr($this->contents, $expected) !== false, 'should contain DSL focused warning');
        });
    });

});

function throwException(&$pattern)
{
    $pattern = preg_quote(sprintf('%s:%d', __FILE__, __LINE__ + 1), '~') . '.*noFilename.*' . $pattern;
    $exception = new Exception('ooops' . PHP_EOL . 'nextline');

    $reflector = new ReflectionClass('Exception');
    $traceProperty = $reflector->getProperty('trace');
    $traceProperty->setAccessible(true);

    $trace = $traceProperty->getValue($exception);
    array_unshift($trace, ['function' => 'noFilename']);
    $traceProperty->setValue($exception, $trace);

    throw $exception;
}

class ExceptionThrower
{
    public function __construct(&$pattern)
    {
        $pattern = preg_quote(sprintf('%s:%d', __FILE__, __LINE__ + 1), '~') . '.*' . $pattern;
        throwException($pattern);
    }
}
