<?php
namespace Peridot\Reporter;

use Peridot\Configuration;
use Peridot\Core\Spec;
use Peridot\Runner\Runner;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AbstractBaseReporter
 * @package Peridot\Reporter
 */
abstract class AbstractBaseReporter implements ReporterInterface
{
    /**
     * @var \Peridot\Configuration
     */
    protected $configuration;

    /**
     * @var \Peridot\Runner\Runner
     */
    protected $runner;

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
     * @var array
     */
    protected $symbols = array(
        'check' => '✓'
    );

    /**
     * @param Configuration $configuration
     * @param Runner $runner
     * @param OutputInterface $output
     */
    public function __construct(Configuration $configuration, Runner $runner, OutputInterface $output)
    {
        $this->configuration = $configuration;
        $this->runner = $runner;
        $this->output = $output;

        $this->runner->on('fail', function(Spec $spec, \Exception $e) {
            $this->errors[] = [$spec, $e];
        });

        $this->runner->on('pass', function() {
            $this->passing++;
        });

        $this->runner->on('pending', function() {
            $this->pending++;
        });

        $this->init();
    }

    /**
     * Helper for colors
     *
     * @param $key
     * @param $text
     * @return string
     */
    public function color($key, $text)
    {
        if (!$this->configuration->areColorsEnabled()) {
            return $text;
        }

        $color = $this->colors[$key];
        return sprintf("%s%s%s", $color['left'], $text, $color['right']);
    }

    /**
     * @param $name
     * @return string
     */
    public function symbol($name)
    {
        return $this->symbols[$name];
    }

    /**
     * Initialize reporter. Setup and listen for runner events
     *
     * @return void
     */
    abstract public function init();
}
