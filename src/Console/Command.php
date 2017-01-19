<?php
namespace Peridot\Console;

use Evenement\EventEmitterInterface;
use Peridot\Configuration;
use Peridot\Core\HasEventEmitterTrait;
use Peridot\Core\TestResult;
use Peridot\Reporter\ReporterFactory;
use Peridot\Runner\RunnerInterface;
use Peridot\Runner\SuiteLoader;
use Peridot\Runner\SuiteLoaderInterface;
use Symfony\Component\Console\Command\Command as ConsoleCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The default Peridot CLI command. Responsible for loading and
 * executing tests.
 *
 * @package Peridot\Console
 */
class Command extends ConsoleCommand
{
    use HasEventEmitterTrait;

    /**
     * @var \Peridot\Runner\RunnerInterface
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
     * @var \Peridot\Runner\SuiteLoaderInterface
     */
    protected $loader;

    /**
     * @param RunnerInterface $runner
     * @param Configuration $configuration
     * @param ReporterFactory $factory
     * @param EventEmitterInterface $eventEmitter
     */
    public function __construct(
        RunnerInterface $runner,
        Configuration $configuration,
        ReporterFactory $factory,
        EventEmitterInterface $eventEmitter
    ) {
        parent::__construct('peridot');
        $this->runner = $runner;
        $this->configuration = $configuration;
        $this->factory = $factory;
        $this->eventEmitter = $eventEmitter;
    }

    /**
     * Set the loader used by the Peridot command
     *
     * @param SuiteLoaderInterface $loader
     * @return $this
     */
    public function setLoader(SuiteLoaderInterface $loader)
    {
        $this->loader = $loader;
        return $this;
    }

    /**
     * Fetch the loader used by the Peridot command. Defaults to
     * a glob based loader
     *
     * @return SuiteLoaderInterface
     */
    public function getLoader()
    {
        if ($this->loader === null) {
            return new SuiteLoader($this->configuration->getGrep());
        }
        return $this->loader;
    }

    /**
     * Set the suite runner used by the Peridot command.
     *
     * @param RunnerInterface $runner
     * @return $this
     */
    public function setRunner(RunnerInterface $runner)
    {
        $this->runner = $runner;
        return $this;
    }

    /**
     * Return the runner used by the Peridot command. Defaults to
     * an instance of Peridot\Runner\Runner.
     *
     * @return RunnerInterface
     */
    public function getRunner()
    {
        return $this->runner;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getSynopsis($short = false)
    {
        return $this->getName() . ' [options] [files]';
    }

    /**
     * Load and run Suites and Tests
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->eventEmitter->emit('peridot.execute', [$input, $output]);
        $this->eventEmitter->emit('peridot.reporters', [$input, $this->factory]);

        if ($input->getOption('reporters')) {
            $this->listReporters($output);

            return 0;
        }

        $this->configuration->setReporters($input->getOption('reporter'));
        $this->eventEmitter->emit('peridot.load', [$this, $this->configuration]);

        return $this->getResult();
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

    /**
     * Return the result as an integer.
     *
     * @return int
     */
    protected function getResult()
    {
        $result = new TestResult($this->eventEmitter);
        $this->getLoader()->load($this->configuration->getPath());
        $this->factory->createComposite($this->configuration->getReporters());
        $this->runner->run($result);

        if ($result->getFailureCount() > 0) {
            return 1;
        }

        if ($result->isFocusedByDsl()) {
            return 2;
        }

        return 0;
    }
}
