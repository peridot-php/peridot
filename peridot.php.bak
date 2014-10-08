<?php
use Peridot\Configuration;
use Peridot\Reporter\ReporterFactory;
use Peridot\Reporter\ReporterInterface;
use Peridot\Runner\Runner;

/**
 * Demonstrate registering a runner via peridot config
 */
return function(Runner $runner, Configuration $config, ReporterFactory $reporters) {
    $counts = ['pass' => 0, 'fail' => 0, 'pending' => 0];

    $runner->on('fail', function() use (&$counts) {
        $counts['fail']++;
    });

    $runner->on('pass', function() use (&$counts) {
        $counts['pass']++;
    });

    $runner->on('pending', function() use (&$counts) {
        $counts['pending']++;
    });

    $reporters->register('basic', 'a simple summary', function(ReporterInterface $reporter) use (&$counts) {
        $output = $reporter->getOutput();
        $reporter->getRunner()->on('end', function() use ($output, &$counts) {
            $output->writeln(sprintf(
                '%d run, %d failed, %d pending',
                $counts['pass'],
                $counts['fail'],
                $counts['pending']
            ));
        });
    });
};
