<?php
namespace Peridot\Console;

use Evenement\EventEmitterInterface;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Application
 * @package Peridot\Console
 */
class Application extends ConsoleApplication
{
    /**
     * @var \Evenement\EventEmitterInterface
     */
    protected $eventEmitter;

    /**
     * Constructor
     *
     * @param EventEmitterInterface $eventEmitter
     */
    public function __construct(EventEmitterInterface $eventEmitter)
    {
        parent::__construct(Version::NAME, Version::NUMBER);
        $this->eventEmitter = $eventEmitter;
        require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Dsl.php';
    }

    /**
     * Run the peridot application
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $this->add(new Command($this->eventEmitter));

        return parent::doRun($input, $output);
    }

    /**
     * Return the peridot input definition
     *
     * @return InputDefinition|\Symfony\Component\Console\Input\InputDefinition
     */
    protected function getDefaultInputDefinition()
    {
        return new InputDefinition();
    }

    /**
     * @param  InputInterface $input
     * @return string
     */
    public function getCommandName(InputInterface $input)
    {
        return 'peridot';
    }
}
