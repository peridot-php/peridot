<?php
namespace Peridot\Console;

use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Application
 * @package Peridot\Console
 */
class Application extends ConsoleApplication
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(Version::NAME, Version::NUMBER);
        require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Dsl.php';
    }

    /**
     * Run the peridot application
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $this->add(new Command());
        return parent::doRun($input, $output);
    }

    /**
     * The default InputDefinition for the application. Leave it to specific
     * Tester objects for specifying further definitions
     *
     * @return InputDefinition
     */
    public function getDefinition()
    {
        return new InputDefinition(array(
            new InputOption('--help', '-h', InputOption::VALUE_NONE, 'Display this help message.')
        ));
    }

    /**
     * @param InputInterface $input
     * @return string
     */
    public function getCommandName(InputInterface $input)
    {
        return 'peridot';
    }
} 
