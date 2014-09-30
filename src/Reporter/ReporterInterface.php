<?php
namespace Peridot\Reporter;

/**
 * Interface ReporterInterface
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
}
