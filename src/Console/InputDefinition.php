<?php
namespace Peridot\Console;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition as Definition;
use Symfony\Component\Console\Input\InputOption;

class InputDefinition extends Definition
{
    /**
     * Define input definition for peridot
     */
    public function __construct()
    {
        parent::__construct([]);
        $this->addArgument(new InputArgument('path', InputArgument::OPTIONAL, 'The path to a directory or file containing specs'));

        $this->addOption(new InputOption('grep', 'g', InputOption::VALUE_REQUIRED, 'Run tests matching <pattern>'));
        $this->addOption(new InputOption('no-colors', 'C', InputOption::VALUE_NONE, 'Disable output colors'));
        $this->addOption(new InputOption('reporter', 'r', InputOption::VALUE_REQUIRED, 'Select reporter to use as listed by --reporters'));
        $this->addOption(new InputOption('bail', 'b', InputOption::VALUE_NONE, 'Stop on failure'));
        $this->addOption(new InputOption('configuration', 'c', InputOption::VALUE_REQUIRED, 'A php file containing peridot configuration'));
        $this->addOption(new InputOption('reporters', null, InputOption::VALUE_NONE, 'List all available reporters'));
        $this->addOption(new InputOption('--help', '-h', InputOption::VALUE_NONE, 'Display this help message.'));
    }

}
