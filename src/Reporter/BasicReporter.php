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
class BasicReporter extends AbstractBaseReporter
{
    /**
     * @var array
     */
    protected $counts = ['pass' => 0, 'fail' => 0];

    /**
     * @param Runner $runner
     * @param OutputInterface $output
     */
    public function init()
    {
        $this->runner->on('fail', function() {
           $this->counts['fail']++;
        });

        $this->runner->on('pass', function() {
           $this->counts['pass']++;
        });

        $this->runner->on('end', function() {
           $this->output->writeln(sprintf('%d run, %d failed', $this->counts['pass'], $this->counts['fail']));
        });
    }
} 
