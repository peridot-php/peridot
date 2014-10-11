<?php
namespace Peridot\Reporter;

/**
 * ReporterInterface is the contract for Peridot reporters.
 *
 * @package Peridot\Reporter
 */
interface ReporterInterface
{
    /**
     * Initialize reporter. Setup and listen for runner events
     *
     * @return void
     */
    public function init();

    /**
     * Render the the text in a color identified by $key
     *
     * @param $key
     * @param $text
     * @return string
     */
    public function color($key, $text);

    /**
     * Render the symbol identified by $name
     *
     * @param $name
     * @return string
     */
    public function symbol($name);

    /**
     * @return \Symfony\Component\Console\Output\OutputInterface
     */
    public function getOutput();

    /**
     * @return \Peridot\Configuration
     */
    public function getConfiguration();

    /**
     * @return \Evenement\EventEmitterInterface
     */
    public function getEventEmitter();
}
