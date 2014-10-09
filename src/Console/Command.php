<?php
namespace Peridot\Console;

use Evenement\EventEmitterInterface;
use Peridot\Configuration;
use Peridot\Core\HasEventEmitterTrait;
use Peridot\Core\SpecResult;
use Peridot\Reporter\ReporterFactory;
use Peridot\Runner\Runner;
use Peridot\Runner\SuiteLoader;
use Symfony\Component\Console\Command\Command as ConsoleCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Command
 * @package Peridot\Console
 */
class Command extends ConsoleCommand
{
    use HasEventEmitterTrait;

    /**
     * @var \Peridot\Runner\Runner
     */
    protected $runner;

    /**
     * @var \Peridot\Configuration
     */
    protected $configuration;

    /**
     * @var \Peridot\Reporter\ReporterFactory
     */
    protected $factory;

    /**
     * Constructor
     */
    public function __construct(
        Runner $runner,
        Configuration $configuration,
        ReporterFactory $factory,
        EventEmitterInterface $eventEmitter
    )
    {
        parent::__construct('peridot');
        $this->runner = $runner;
        $this->configuration = $configuration;
        $this->factory = $factory;
        $this->eventEmitter = $eventEmitter;
    }

    /**
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->eventEmitter->emit('peridot.preExecute', [
            $this->runner,
            $this->configuration,
            $this->factory,
            $input,
            $output
        ]);

        if ($input->getOption('reporters')) {
            $this->listReporters($output);

            return 0;
        }

        if ($reporter = $input->getOption('reporter')) {
            $this->configuration->setReporter($reporter);
        }

        $result = new SpecResult($this->eventEmitter);
        $loader = new SuiteLoader($this->configuration->getGrep());
        $loader->load($this->configuration->getPath());
        $this->factory->create($this->configuration->getReporter());
        $this->runner->run($result);

        if ($result->getFailureCount() > 0) {
            return 1;
        }

        return 0;
    }

    /**
     * Output available reporters
     *
     * @param OutputInterface $output
     */
    protected function listReporters(OutputInterface $output)
    {
        $output->writeln("");
        foreach ($this->factory->getReporters() as $name => $info) {
            $output->writeln(sprintf("    %s - %s", $name, $info['description']));
        }
        $output->writeln("");
    }
}
