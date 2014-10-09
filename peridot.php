<?php
use Evenement\EventEmitterInterface;
use Peridot\Configuration;
use Peridot\Reporter\ReporterFactory;
use Peridot\Reporter\ReporterInterface;

/**
 * Demonstrate registering a runner via peridot config
 */
return function(EventEmitterInterface $emitter) {
    $counts = ['pass' => 0, 'fail' => 0, 'pending' => 0];

    $emitter->on('spec.failed', function() use (&$counts) {
        $counts['fail']++;
    });

    $emitter->on('spec.passed', function() use (&$counts) {
        $counts['pass']++;
    });

    $emitter->on('spec.pending', function() use (&$counts) {
        $counts['pending']++;
    });

    $emitter->on('peridot.preExecute', function($runner, $config, $reporters) use (&$counts, $emitter) {
        $reporters->register('basic', 'a simple summary', function(ReporterInterface $reporter) use (&$counts, $emitter) {
            $output = $reporter->getOutput();
            $emitter->on('runner.end', function() use ($output, &$counts) {
                $output->writeln(sprintf(
                    '%d run, %d failed, %d pending',
                    $counts['pass'],
                    $counts['fail'],
                    $counts['pending']
                ));
            });
        });
    });
};
