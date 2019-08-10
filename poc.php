<?php

// run with: php poc.php --colors=auto

use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Runner\BaseTestRunner;
use PHPUnit\Runner\TestSuiteSorter;
use PHPUnit\TextUI\Command;
use PHPUnit\Util\TestDox\CliTestDoxPrinter;

require_once __DIR__ . '/vendor/autoload.php';

final class CallableTestCase extends TestCase {
    private $fn;
    public $suiteName;

    public function __call($method, $arguments) {
        if ($method === $this->getName(false)) {
            return ($this->fn)(...$arguments);
        } else if (is_callable([$this, $method])) { // allow access to protected methods from fn context
            return $this->{$method}(...$arguments);
        } else {
            throw new BadMethodCallException('Bad method: ' . $method);
        }
    }

    public static function createFromFn(string $suiteName, string $name, callable $fn, array $data = [], string $dataName = ''): self {
        $self = new self($name, $data, $dataName);
        $self->fn = Closure::fromCallable($fn)->bindTo($self);
        $self->suiteName = $suiteName;
        return $self;
    }
}

final class GlobalContext {
    private $suites;

    private function __construct() {
        $this->suites = new SplStack();
        $this->suites->push(new TestSuite('Main'));
    }

    public static function instance(): self {
        static $ctx;
        if ($ctx) {
            return $ctx;
        }

        $ctx = new self();
        return $ctx;
    }

    public function addSuite(string $name, callable $fn): TestSuite {
        $suite = new TestSuite($name);
        $this->suites->push($suite);
        $fn($suite);
        $this->suites->pop();
        $this->suites->top()->addTestSuite($suite);
        return $suite;
    }

    public function addTestToCurrentSuite(string $name, callable $fn, ?callable $provideData = null): void {
        /** @var TestSuite $suite */
        $suite = $this->suites->top();
        if ($provideData) {
            $providedData = $provideData();
            if (!is_iterable($providedData)) {
                throw new RuntimeException('Data provider must return an iterable');
            }
            foreach ($providedData as $dataName => $data) {
                $suite->addTest(CallableTestCase::createFromFn($suite->getName(), $name, $fn, $data, (string) $dataName));
            }
        } else {
            $suite->addTest(CallableTestCase::createFromFn($suite->getName(), $name, $fn));
        }
    }

    public function rootSuite(): TestSuite {
        return $this->suites->bottom();
    }
}

final class PeridotCliTestDoxPrinter extends CliTestDoxPrinter {
    protected function registerTestResult(Test $test, ?\Throwable $t, int $status, float $time, bool $verbose): void {
        $testName = TestSuiteSorter::getTestSorterUID($test);
        $status   = $status ?? BaseTestRunner::STATUS_UNKNOWN;

        $result = [
            'className'  => $this->prettifier->prettifyTestClass($test->suiteName),
            'testName'   => $testName,
            'testMethod' => $this->prettifier->prettifyTestCase($test),
            'message'    => '',
            'status'     => $status,
            'time'       => $time,
            'verbose'    => $verbose,
        ];

        if ($t !== null) {
            $result['message'] = $this->formatTestResultMessage($t, $result);
        }

        $this->testResults[$this->testIndex]  = $result;
        $this->testNameResultIndex[$testName] = $this->testIndex;
    }
}

final class PeridotCommand extends Command {
    private $test;

    public function withTest(Test $test): Command {
        $self = clone $this;
        $self->test = $test;
        return $self;
    }

    protected function handleArguments(array $argv): void {
        $this->arguments['test'] = $this->test;
        $this->arguments['printer'] = PeridotCliTestDoxPrinter::class;
        parent::handleArguments($argv);
    }
}

function describe(string $name, callable $fn): void {
    GlobalContext::instance()->addSuite($name, $fn);
}

function it(string $name, callable $fn, ?callable $provideData = null): void {
    GlobalContext::instance()->addTestToCurrentSuite($name, $fn, $provideData);
}

function main(array $argv) {
    $command = new PeridotCommand();

    describe('Suite 1', function() {
        it('can do one thing', function() {
            $this->assertEquals('a', 'a');
        });
        it('can utilize data providers', function($a, $b) {
            $this->assertEquals($a, $b);
        }, function() {
            yield '1 = 1' => [1, 1];
            yield '2 = 2' => [2, 3];
        });
        it('can assert exceptions', function() {
            $this->expectException('RuntimeException');
            throw new \RuntimeException('hi');
        });
        it('can use mocking', function() {
            /** @var SplFileInfo $mock */
            $mock = $this->createMock('SplFileInfo');
            $mock->expects($this->once())->method('openFile');
            $mock->openFile();
        });
        describe('sub context', function() {
           it('something in sub context', function() {
               $this->assertEquals('a', 'a');
           });
        });
    });

    $command->withTest(GlobalContext::instance()->rootSuite())->run($argv);
}

main($argv);
