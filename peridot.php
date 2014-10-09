<?php
use Evenement\EventEmitterInterface;
use Peridot\Configuration;
use Peridot\Console\InputDefinition;
use Peridot\Reporter\ReporterFactory;
use Peridot\Reporter\ReporterInterface;
use Symfony\Component\Console\Input\InputOption;

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

    $emitter->on('peridot.start', function(InputDefinition $definition) {
        $definition->addOption(new InputOption("banner", null, InputOption::VALUE_REQUIRED, "Custom banner text"));
    });

    $emitter->on('peridot.preExecute', function($runner, $config, $reporters, $input) use (&$counts, $emitter) {
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
