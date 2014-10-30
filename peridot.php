<?php

use Evenement\EventEmitterInterface;
use Peridot\Console\Environment;
use Peridot\Reporter\CodeCoverageReporters;
use Peridot\Reporter\ReporterInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * Demonstrate registering a runner via peridot config
 */
return function(EventEmitterInterface $emitter) {
    (new CodeCoverageReporters($emitter))->register();

    $counts = ['pass' => 0, 'fail' => 0, 'pending' => 0];

    $emitter->on('test.failed', function() use (&$counts) {
        $counts['fail']++;
    });

    $emitter->on('test.passed', function() use (&$counts) {
        $counts['pass']++;
    });

    $emitter->on('test.pending', function() use (&$counts) {
        $counts['pending']++;
    });

    $shouldCover = getenv('COVER');
    if ($shouldCover) {
        $coverage = new PHP_CodeCoverage();
        $emitter->on('runner.start', function() use ($coverage) {
            $coverage->start('peridot');
        });

        $emitter->on('runner.end', function() use ($coverage) {
            $coverage->stop();
            $writer = new PHP_CodeCoverage_Report_HTML();
            $writer->process($coverage, __DIR__ . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'report');
        });
    }

    $emitter->on('peridot.start', function(Environment $env) use (&$coverage) {
        $env->getDefinition()->option("banner", null, InputOption::VALUE_REQUIRED, "Custom banner text");
    });

    $emitter->on('peridot.reporters', function($input, $reporters) use (&$counts) {
        $banner = $input->getOption('banner');
        $reporters->register('basic', 'a simple summary', function(ReporterInterface $reporter) use (&$counts, $banner) {
            $output = $reporter->getOutput();

            $reporter->getEventEmitter()->on('runner.start', function() use ($banner, $output) {
                $output->writeln($banner);
            });

            $reporter->getEventEmitter()->on('runner.end', function() use ($output, &$counts) {
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
