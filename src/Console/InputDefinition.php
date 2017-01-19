<?php
namespace Peridot\Console;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition as Definition;
use Symfony\Component\Console\Input\InputOption;

/**
 * The InputDefinition for Peridot defines what command line arguments
 * and options are available by default.
 *
 * @package Peridot\Console
 */
class InputDefinition extends Definition
{
    /**
     * Define input definition for peridot
     */
    public function __construct()
    {
        parent::__construct([]);
        $this->addArgument(new InputArgument('path', InputArgument::OPTIONAL, 'The path to a directory or file containing specs'));

        $this->addOption(new InputOption('focus', 'f', InputOption::VALUE_REQUIRED, 'Run tests matching <pattern>'));
        $this->addOption(new InputOption('skip', 's', InputOption::VALUE_REQUIRED, 'Skip tests matching <pattern>'));
        $this->addOption(new InputOption('grep', 'g', InputOption::VALUE_REQUIRED, 'Run tests with filenames matching <pattern> <comment>(default: *.spec.php)</comment>'));
        $this->addOption(new InputOption('no-colors', 'C', InputOption::VALUE_NONE, 'Disable output colors'));
        $this->addOption(new InputOption('--force-colors', null, InputOption::VALUE_NONE, 'Force output colors'));
        $this->addOption(new InputOption('reporter', 'r', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Select which reporter(s) to use', ['spec']));
        $this->addOption(new InputOption('bail', 'b', InputOption::VALUE_NONE, 'Stop on failure'));
        $this->addOption(new InputOption('configuration', 'c', InputOption::VALUE_REQUIRED, 'A php file containing peridot configuration'));
        $this->addOption(new InputOption('reporters', null, InputOption::VALUE_NONE, 'List all available reporters'));
        $this->addOption(new InputOption('--version', '-V', InputOption::VALUE_NONE, 'Display the Peridot version number'));
        $this->addOption(new InputOption('--help', '-h', InputOption::VALUE_NONE, 'Display this help message.'));
    }

    /**
     * Add an argument
     *
     * @param  string $name
     * @param  null          $mode
     * @param  string        $description
     * @param  null          $default
     * @return InputDefinition
     */
    public function argument($name, $mode = null, $description = '', $default = null)
    {
        $argument = new InputArgument($name, $mode, $description, $default);
        $this->addArgument($argument);

        return $this;
    }

    /**
     * Add an option
     *
     * @param  string $name
     * @param  null        $shortcut
     * @param  null        $mode
     * @param  string      $description
     * @param  null        $default
     * @return InputDefinition
     */
    public function option($name, $shortcut = null, $mode = null, $description = '', $default = null)
    {
        $option = new InputOption($name, $shortcut, $mode, $description, $default);
        $this->addOption($option);

        return $this;
    }
}
