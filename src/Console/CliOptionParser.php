<?php
namespace Peridot\Console;

use Symfony\Component\Console\Input\ArgvInput;

/**
 * The CliOptionParser parser searches an array of
 * arguments for the specified options and returns their
 * values.
 *
 * @package Peridot\Console
 */
class CliOptionParser
{
    /**
     * CLI options to search for
     *
     * @var array
     */
    protected $search;

    /**
     * The CLI arguments to search against
     *
     * @var array
     */
    protected $arguments;

    /**
     * @var array $search
     * @var array $arguments
     */
    public function __construct(array $search, array $arguments)
    {
        $this->search = $search;
        $this->arguments = $arguments;
    }

    /**
     * Parse arguments to find any options specified
     * in the search array
     *
     * @return array $parsed
     */
    public function parse()
    {
        $input = new ArgvInput($this->arguments);
        $parsed = [];

        foreach ($this->search as $option) {
            if ($input->hasParameterOption($option)) {
                $name = ltrim($option, '-');
                $parsed[$name] = $input->getParameterOption($option);
            }
        }

        return $parsed;
    }
}
