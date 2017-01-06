<?php
namespace Peridot\Reporter;

use Evenement\EventEmitterInterface;
use Peridot\Configuration;
use Peridot\Core\HasEventEmitterTrait;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Combines multiple reporters.
 *
 * @package Peridot\Reporter
 */
class CompositeReporter extends AbstractBaseReporter
{
    /**
     * @var array
     */
    private $reporters;

    /**
     * @param array $reporters
     * @param Configuration $configuration
     * @param OutputInterface $output
     * @param EventEmitterInterface $eventEmitter
     */
    public function __construct(
        array $reporters,
        Configuration $configuration,
        OutputInterface $output,
        EventEmitterInterface $eventEmitter
    ) {
        $this->reporters = $reporters;

        parent::__construct($configuration, $output, $eventEmitter);
    }

    /**
     * Return the wrapped reporters.
     *
     * @return array
     */
    public function getReporters()
    {
        return $this->reporters;
    }

    /**
     * Initialize reporter. Setup and listen for runner events.
     *
     * @return void
     */
    public function init()
    {
        $this->eventEmitter->on('runner.end', [$this, 'onRunnerEnd']);
    }

    /**
     * @param \Evenement\EventEmitterInterface $eventEmitter
     */
    public function setEventEmitter(EventEmitterInterface $eventEmitter)
    {
        parent::setEventEmitter($eventEmitter);

        array_map(function (ReporterInterface $reporter) use ($eventEmitter) {
            $reporter->setEventEmitter($eventEmitter);
        }, $this->reporters);

        return $this;
    }

    public function onRunnerEnd()
    {
        $stdout = $this->getOutput();

        array_map(function (ReporterInterface $reporter) use ($stdout) {
            $output = $reporter->getOutput();

            if ($output instanceof BufferedOutput && $content = $output->fetch()) {
                $stdout->writeln('');
                $stdout->write($content);
            }
        }, $this->reporters);
    }
}
