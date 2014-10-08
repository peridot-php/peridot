<?php
namespace Peridot\Runner;

/**
 * Class SuiteLoader
 * @package Peridot\Runner
 */
class SuiteLoader
{
    /**
     * @var string
     */
    protected $pattern;

    /**
     * Constructor
     */
    public function __construct($pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * Load specs. Runs 'include' on all specs
     * returned by getSpecs
     *
     * @param $path
     */
    public function load($path)
    {
        $specs = $this->getSpecs($path);
        foreach ($specs as $spec) {
            include $spec;
        }
    }

    /**
     * Search a path for a provided file or scan a
     * directory structure for files matching the loader's
     * pattern
     *
     * @param $path
     * @return array
     * @throws \RuntimeException
     */
    public function getSpecs($path)
    {
        if (is_file($path)) {
            return [$path];
        }
        if (! file_exists($path)) {
            throw new \RuntimeException("Cannot load path $path");
        }
        $pattern = $path . '/' . $this->pattern;

        return $this->globRecursive($pattern);
    }

    /**
     * Simple recursive glob
     *
     * @link http://php.net/manual/en/function.glob.php#106595
     * @param $pattern
     * @param  int   $flags
     * @return array
     */
    protected function globRecursive($pattern, $flags = 0)
    {
        $files = glob($pattern, $flags);

        foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
            $files = array_merge($files, $this->globRecursive($dir .'/'. basename($pattern), $flags));
        }

        return $files;
    }
}
