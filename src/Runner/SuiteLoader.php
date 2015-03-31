<?php
namespace Peridot\Runner;

/**
 * SuiteLoader will recursively load test files given a glob friendly
 * pattern.
 *
 * @package Peridot\Runner
 */
class SuiteLoader implements SuiteLoaderInterface
{
    /**
     * @var string
     */
    protected $pattern;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @param string $pattern
     */
    public function __construct($pattern)
    {
        $this->pattern = $pattern;
        $this->context = Context::getInstance();
    }

    /**
     * {@inheritdoc}
     *
     * @param $path
     */
    public function load($path)
    {
        $tests = $this->getTests($path);
        foreach ($tests as $test) {
            $this->context->setFile($test);
            include $test;
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param $path
     * @return array
     * @throws \RuntimeException
     */
    public function getTests($path)
    {
        if (is_file($path)) {
            return [$path];
        }
        if (! file_exists($path)) {
            throw new \RuntimeException("Cannot load path $path");
        }
        $pattern = realpath($path) . '/' . $this->pattern;

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
