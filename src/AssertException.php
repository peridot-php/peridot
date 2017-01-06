<?php
namespace Peridot;

use Exception;
use ReflectionClass;

/**
 * Represents a failed assert() call.
 *
 * @package Peridot
 */
class AssertException extends Exception
{
    /**
     * Handle a failed assert() call.
     *
     * @param string $file
     * @param int $line
     * @param string $expression
     * @param string $description
     *
     * @return self
     */
    public static function handle($file, $line, $expression, $description)
    {
        throw new self($file, $line, $expression, $description);
    }

    /**
     * Construct a new assert exception.
     *
     * @param string $file
     * @param int $line
     * @param string $expression
     * @param string $description
     */
    public function __construct($file, $line, $expression, $description)
    {
        if ($expression) {
            $message = sprintf('%s %s', $expression, $description);
        } else {
            $message = $description;
        }

        parent::__construct($message);

        $this->file = $file;
        $this->line = $line;

        $reflector = new ReflectionClass('Exception');
        $traceProperty = $reflector->getProperty('trace');
        $traceProperty->setAccessible(true);
        $traceProperty->setValue($this, array());
    }
}
