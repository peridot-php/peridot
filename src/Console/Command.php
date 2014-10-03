<?php
namespace Peridot\Console;

use Peridot\Configuration;
use Peridot\Core\SpecResult;
use Peridot\Reporter\ReporterFactory;
use Peridot\Runner\Context;
use Peridot\Runner\Runner;
use Peridot\Runner\SuiteLoader;
use Symfony\Component\Console\Command\Command as ConsoleCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Command
 * @package Peridot\Console
 */
class Command extends ConsoleCommand
{
    public function __construct()
    {
        parent::__construct('peridot');
    }

    /**
     * Configure the command
     */
    protected function configure()
    {
        $this
            ->addArgument('path', InputArgument::OPTIONAL, 'The path to a directory or file containing specs')
            ->addOption('grep', 'g', InputOption::VALUE_REQUIRED, 'Run tests matching <pattern>')
            ->addOption('no-colors', 'C', InputOption::VALUE_NONE, 'Disable output colors')
            ->addOption('reporter', 'r', InputOption::VALUE_REQUIRED, 'Select reporter to use as listed by --reporters')
            ->addOption('bail', 'b', InputOption::VALUE_NONE, 'Stop on failure')
            ->addOption('configuration', 'c', InputOption::VALUE_OPTIONAL, 'A php file containing peridot configuration')
            ->addOption('reporters', null, InputOption::VALUE_NONE, 'List all available reporters');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configuration = $this->getConfiguration($input);
        $runner = new Runner(Context::getInstance()->getCurrentSuite(), $configuration);
        $factory = new ReporterFactory($configuration, $runner, $output);

        if (file_exists($configuration->getConfigurationFile())) {
            $this->loadConfiguration($runner, $configuration, $factory);
        }

        if ($input->getOption('reporters')) {
            $this->listReporters($factory, $output);
            return 0;
        }

        //Defer selection of reporter to account for user registered reporters
        if ($reporter = $input->getOption('reporter')) {
            $configuration->setReporter($reporter);
        }

        $result = new SpecResult();
        $loader = new SuiteLoader($configuration->getGrep());
        $loader->load($configuration->getPath());
        $factory->create($configuration->getReporter());
        $runner->run($result);

        if ($result->getFailureCount() > 0) {
            return 1;
        }
        return 0;
    }

    /**
     * Read configuration information from input
     *
     * @param InputInterface $input
     * @return Configuration
     */
    protected function getConfiguration(InputInterface $input)
    {
        $configuration = new Configuration();

        if ($path = $input->getArgument('path')) {
            $configuration->setPath($path);
        }

        if ($grep = $input->getOption('grep')) {
            $configuration->setGrep($grep);
        }

        if ($noColors = $input->getOption('no-colors')) {
            $configuration->disableColors();
        }

        if ($bail = $input->getOption('bail')) {
            $configuration->stopOnFailure();
        }

        if ($config = $input->getOption('configuration')) {
            $configuration->setConfigurationFile($config);
        }

        return $configuration;
    }

    /**
     * Output available reporters
     *
     * @param ReporterFactory $factory
     * @param OutputInterface $output
     */
    protected function listReporters(ReporterFactory $factory, OutputInterface $output)
    {
        $output->writeln("");
        foreach ($factory->getReporters() as $name => $info) {
            $output->writeln(sprintf("    %s - %s", $name, $info['description']));
        }
        $output->writeln("");
    }

    /**
     * Load configuration file. If the configuration file returns
     * a callable, it will be executed with the runner, configuration, and reporter factory
     *
     * @param Runner $runner
     * @param Configuration $configuration
     * @param ReporterFactory $reporters
     */
    protected function loadConfiguration(Runner $runner, Configuration $configuration, ReporterFactory $reporters)
    {
        $func = include $configuration->getConfigurationFile();
        if (is_callable($func)) {
            $func($runner, $configuration, $reporters);
        }
    }
}
