<?php
namespace Peridot\Console;

use Peridot\Configuration;
use Peridot\Core\SpecResult;
use Peridot\Reporter\SpecReporter;
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
            ->addArgument('path', InputArgument::REQUIRED, 'The path to a directory or file containing specs')
            ->addOption('grep', 'g', InputOption::VALUE_REQUIRED, 'Run tests matching <pattern>');

    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getArgument('path');
        $configuration = $this->getConfiguration($input);

        $result = new SpecResult();
        $loader = new SuiteLoader($configuration->getGrep());
        $loader->load($path);
        $runner = new Runner(Context::getInstance()->getCurrentSuite());
        $reporter = new SpecReporter($runner, $output);
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

        if ($grep = $input->getOption('grep')) {
            $configuration->setGrep($grep);
        }

        return $configuration;
    }
} 
