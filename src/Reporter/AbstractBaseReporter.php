<?php
namespace Peridot\Reporter;

use Evenement\EventEmitterInterface;
use Peridot\Configuration;
use Peridot\Core\HasEventEmitterTrait;
use Peridot\Core\Test;
use Symfony\Component\Console\Output\OutputInterface;

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
        'white' => ['left' => '<fg=white>', 'right' => '</fg=white>'],
        'success' => ['left' => '<fg=green>', 'right' => '</fg=green>'],
        'error' => ['left' => '<fg=red>', 'right' => '</fg=red>'],
        'muted' => ['left' => "\033[90m", 'right' => "\033[0m"],
        'pending' => ['left' => '<fg=cyan>', 'right' => '</fg=cyan>'],
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
    )
    {
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
        if (!$this->configuration->areColorsEnabled() || !$this->hasColorSupport()) {
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
     * @return double|integer
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
        if ($this->errors) {
            $this->output->writeln($this->color('error', sprintf("  %d failing", count($this->errors))));
        }
        if ($this->pending) {
            $this->output->writeln($this->color('pending', sprintf("  %d pending", $this->pending)));
        }
        $this->output->writeln("");
        $errorCount = count($this->errors);
        for ($i = 0; $i < $errorCount; $i++) {
            list($test, $error) = $this->errors[$i];
            $this->output->writeln(sprintf("  %d)%s:", $i + 1, $test->getTitle()));
            $this->output->writeln($this->color('error', sprintf("     %s", $error->getMessage())));
            $trace = preg_replace('/^#/m', "      #", $error->getTraceAsString());
            $this->output->writeln($this->color('muted', $trace));
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

        if (method_exists($this->output, 'getStream')) {
            return $this->hasTty();
        }

        return false;
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
     * Register events tracking state relevant to all reporters.
     */
    private function registerEvents()
    {
        $this->eventEmitter->on('runner.start', ['\PHP_Timer', 'start']);

        $this->eventEmitter->on('runner.end', function () {
            $this->time = \PHP_Timer::stop();
        });

        $this->eventEmitter->on('test.failed', function (Test $test, \Exception $e) {
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
     * Return true if reporter is being used on windows
     *
     * @return bool
     */
    private function isOnWindows()
    {
        return DIRECTORY_SEPARATOR == '\\';
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
     * Determine if reporter is reporting to a tty terminal
     *
     * @return bool
     */
    private function hasTty()
    {
        return function_exists('posix_isatty') && @posix_isatty($this->output->getStream());
    }

    /**
     * Initialize reporter. Setup and listen for events
     *
     * @return void
     */
    abstract public function init();
}
