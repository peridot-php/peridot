<?php
namespace Peridot\Reporter;

use Peridot\Runner\Runner;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BasicReporter
 *
 * A reporter that just displays a simple pass/fail count
 *
 * @package Peridot\Reporter
 */
class BasicReporter
{
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
    protected $counts = ['pass' => 0, 'fail' => 0];

    /**
     * @param Runner $runner
     * @param OutputInterface $output
     */
    public function __construct(Runner $runner, OutputInterface $output)
    {
        $this->runner = $runner;
        $this->output = $output;

        $runner->on('fail', function() {
           $this->counts['fail']++;
        });

        $runner->on('pass', function() {
           $this->counts['pass']++;
        });

        $runner->on('end', function() {
           $this->output->writeln(sprintf('%d run, %d failed', $this->counts['pass'], $this->counts['fail']));
        });
    }
} 
