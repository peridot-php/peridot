<?php
namespace Peridot\Reporter;

use Evenement\EventEmitterInterface;
use Peridot\Configuration;
use Peridot\Core\HasEventEmitterTrait;
use Peridot\Core\Test;
use Peridot\Core\TestInterface;
use Peridot\Core\TestResult;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;

/**
 * The base class for all Peridot reporters. Sits on top of an OutputInterface
 * and an EventEmitter in order to report Peridot results.
 *
 * @package Peridot\Reporter
 */
abstract class AbstractBaseReporter implements ReporterInterface
{
    use HasEventEmitterTrait;

    /**
     * @var \Peridot\Configuration
     */
    protected $configuration;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @var int
     */
    protected $passing = 0;

    /**
     * @var int
     */
    protected $pending = 0;

    /**
     * @var double|integer
     */
    protected $time;

    /**
     * Maps color names to left and right color sequences.
     *
     * @var array
     */
    protected $colors = array(
        'white' => ['left' => "\033[37m", 'right' => "\033[39m"],
        'success' => ['left' => "\033[32m", 'right' => "\033[39m"],
        'error' => ['left' => "\033[31m", 'right' => "\033[39m"],
        'warning' => ['left' => "\033[33m", 'right' => "\033[39m"],
        'muted' => ['left' => "\033[90m", 'right' => "\033[0m"],
        'pending' => ['left' => "\033[36m", 'right' => "\033[39m"],
    );

    /**
     * Maps symbol names to symbols
     *
     * @var array
     */
    protected $symbols = array(
        'check' => '✓'
    );

    /**
     * @param Configuration $configuration
     * @param OutputInterface $output
     * @param EventEmitterInterface $eventEmitter
     */
    public function __construct(
        Configuration $configuration,
        OutputInterface $output,
        EventEmitterInterface $eventEmitter
    ) {
        $this->configuration = $configuration;
        $this->output = $output;
        $this->eventEmitter = $eventEmitter;

        $this->registerSymbols();

        $this->registerEvents();

        $this->init();
    }

    /**
     * Given a color name, colorize the provided text in that
     * color
     *
     * @param $key
     * @param $text
     * @return string
     */
    public function color($key, $text)
    {
        $colorsEnabled = $this->configuration->areColorsEnabled() && $this->hasColorSupport();
        $colorsEnabledExplicit = $this->configuration->areColorsEnabledExplicit();

        if (!$colorsEnabled && !$colorsEnabledExplicit) {
            return $text;
        }

        $color = $this->colors[$key];

        return sprintf("%s%s%s", $color['left'], $text, $color['right']);
    }

    /**
     * Fetch a symbol by name
     *
     * @param $name
     * @return string
     */
    public function symbol($name)
    {
        return $this->symbols[$name];
    }

    /**
     * Return the OutputInterface associated with the Reporter
     *
     * @return \Symfony\Component\Console\Output\OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Return the Configuration associated with the Reporter
     *
     * @return \Peridot\Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Set the run time to report.
     *
     * @param float $time
     */
    public function setTime($time)
    {
        $this->time = $time;
    }

    /**
     * Get the run time to report.
     *
     * @return float
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Output result footer
     */
    public function footer()
    {
        $this->output->write($this->color('success', sprintf("\n  %d passing", $this->passing)));
        $this->output->writeln(sprintf($this->color('muted', " (%s)"), \PHP_Timer::secondsToTimeString($this->getTime())));
        if (! empty($this->errors)) {
            $this->output->writeln($this->color('error', sprintf("  %d failing", count($this->errors))));
        }
        if ($this->pending) {
            $this->output->writeln($this->color('pending', sprintf("  %d pending", $this->pending)));
        }
        $this->output->writeln("");
        $errorCount = count($this->errors);
        for ($i = 0; $i < $errorCount; $i++) {
            list($test, $error) = $this->errors[$i];
            $this->outputError($i + 1, $test, $error);
            $this->output->writeln('');
        }
    }

    /**
     * Output result warnings
     *
     * @param TestResult $result
     */
    public function warnings(TestResult $result)
    {
        if ($result->isFocusedByDsl()) {
            $this->output->writeln($this->color('warning', 'WARNING: Tests have been focused programmatically.'));
        }
    }

    /**
     * Output a test failure.
     *
     * @param int $errorNumber
     * @param TestInterface $test
     * @param $exception - an exception like interface with ->getMessage(), ->getTrace()
     */
    protected function outputError($errorNumber, TestInterface $test, $exception)
    {
        $this->output->writeln(sprintf("  %d)%s:", $errorNumber, $test->getTitle()));

        $message = sprintf("     %s", str_replace(PHP_EOL, PHP_EOL . "     ", $exception->getMessage()));
        $this->output->writeln($this->color('error', $message));

        $location = sprintf('     at %s:%d', $exception->getFile(), $exception->getLine());
        $this->output->writeln($location);

        $this->outputTrace($exception->getTrace());
    }

    /**
     * Output a stack trace.
     *
     * @param array $trace
     */
    protected function outputTrace(array $trace)
    {
        foreach ($trace as $index => $entry) {
            if (isset($entry['class'])) {
                $function = $entry['class'] . $entry['type'] . $entry['function'];
            } else {
                $function = $entry['function'];
            }

            if (strncmp($function, 'Peridot\\', 8) === 0) {
                break;
            }

            $this->output->writeln($this->color('muted', $this->renderTraceEntry($index, $entry, $function)));
        }
    }

    /**
     * Determine if colorized output is supported by the reporters output.
     * Taken from Symfony's console output with some slight modifications
     * to use the reporter's output stream
     *
     * @return bool
     */
    protected function hasColorSupport()
    {
        if ($this->isOnWindows()) {
            return $this->hasAnsiSupport();
        }

        return $this->hasTty();
    }

    /**
     * Register reporter symbols, additionally checking OS compatibility.
     */
    protected function registerSymbols()
    {
        //update symbols for windows
        if ($this->isOnWindows()) {
            $this->symbols['check'] = chr(251);
        }
    }

    /**
     * Return true if reporter is being used on windows
     *
     * @return bool
     */
    protected function isOnWindows()
    {
        return DIRECTORY_SEPARATOR == '\\';
    }

    /**
     * Register events tracking state relevant to all reporters.
     */
    private function registerEvents()
    {
        $this->eventEmitter->on('runner.end', [$this, 'setTime']);

        $this->eventEmitter->on('test.failed', function (Test $test, $e) {
            $this->errors[] = [$test, $e];
        });

        $this->eventEmitter->on('test.passed', function () {
            $this->passing++;
        });

        $this->eventEmitter->on('test.pending', function () {
            $this->pending++;
        });
    }

    /**
     * Determine if the terminal has ansicon support
     *
     * @return bool
     */
    private function hasAnsiSupport()
    {
        return false !== getenv('ANSICON') || 'ON' === getenv('ConEmuANSI');
    }

    /**
     * Determine if reporter is reporting to a tty terminal.
     *
     * @return bool
     */
    private function hasTty()
    {
        if (! $this->output instanceof StreamOutput) {
            return false;
        }

        if (getenv("PERIDOT_TTY")) {
            return true;
        }

        return $this->isTtyTerminal($this->output);
    }

    /**
     * See if stream output is a tty terminal.
     *
     * @return bool
     */
    private function isTtyTerminal(StreamOutput $output)
    {
        $tty = function_exists('posix_isatty') && @posix_isatty($output->getStream());
        if ($tty) {
            putenv("PERIDOT_TTY=1");
        }
        return $tty;
    }

    /**
     * Initialize reporter. Setup and listen for events
     *
     * @return void
     */
    abstract public function init();

    private function renderTraceEntry($index, array $entry, $function)
    {
        if (isset($entry['file'])) {
            $location = sprintf(' (%s:%d)', $entry['file'], $entry['line']);
        } else {
            $location = '';
        }

        return sprintf('       #%d %s%s', $index, $function, $location);
    }
}
