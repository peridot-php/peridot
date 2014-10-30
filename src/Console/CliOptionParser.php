<?php
namespace Peridot\Console;

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
        $parsed = [];
        $count = count($this->arguments);
        for ($i = 1; $i < $count; $i++) {
            $previous = $this->arguments[$i - 1];
            $arg = $this->arguments[$i];

            $needle = array_reduce($this->search, function ($result, $search) use ($previous) {
                return ($previous == $search) ? $search : $result;
            });

            if (!$needle) {
                continue;
            }

            $parsed[str_replace('-', '', $previous)] = $arg;
        }
        return $parsed;
    }
}
