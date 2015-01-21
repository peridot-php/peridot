<?php
namespace Peridot;

/**
 * Configuration stores configured values used through the Peridot application
 * lifecycle.
 *
 * @package Peridot
 */
class Configuration
{
    /**
     * @var boolean
     */
    protected $colorsEnabled = true;

    /**
     * @var string
     */
    protected $grep = '*.spec.php';

    /**
     * @var string
     */
    protected $reporter = 'spec';

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $configurationFile;

    /**
     * @var string
     */
    protected $dsl;

    /**
     * @var bool
     */
    protected $stopOnFailure = false;

    public function __construct()
    {
        $this->path = getcwd();
        $this->configurationFile = getcwd() . DIRECTORY_SEPARATOR . 'peridot.php';
        $this->dsl = __DIR__ . DIRECTORY_SEPARATOR . 'Dsl.php';
    }

    /**
     * Set the pattern used to load tests
     *
     * @param string $grep
     * @return $this
     */
    public function setGrep($grep)
    {
        return $this->write('grep', $grep);
    }

    /**
     * Returns the pattern used to load tests
     *
     * @return string
     */
    public function getGrep()
    {
        return $this->grep;
    }

    /**
     * Set the name of the reporter to use
     *
     * @param string $reporter
     * @return $this
     */
    public function setReporter($reporter)
    {
        return $this->write('reporter', $reporter);
    }

    /**
     * Return the name of the reporter configured for use
     *
     * @return string
     */
    public function getReporter()
    {
        return $this->reporter;
    }

    /**
     * Set the path to load tests from
     *
     * @param string $path
     * @return $this
     */
    public function setPath($path)
    {
        return $this->write('path', $path);
    }

    /**
     * Return the path being searched for tests
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Disable output colors
     *
     * @return $this
     */
    public function disableColors()
    {
        return $this->write('colorsEnabled', false);
    }

    /**
     * Check if output colors are disabled
     *
     * @return boolean
     */
    public function areColorsEnabled()
    {
        return $this->colorsEnabled;
    }

    /**
     * Stop the suite runner when a failure occurs
     *
     * @return $this
     */
    public function stopOnFailure()
    {
        return $this->write('stopOnFailure', true);
    }

    /**
     * Check if the suite runner should stop on failure
     *
     * @return bool
     */
    public function shouldStopOnFailure()
    {
        return $this->stopOnFailure;
    }

    /**
     * Set the path to a Peridot configuration file
     *
     * @param string $configurationFile
     * @return $this
     */
    public function setConfigurationFile($configurationFile)
    {
        $search = [$configurationFile, getcwd() . DIRECTORY_SEPARATOR . $configurationFile];
        $found = array_filter($search, 'file_exists');

        if (count($found) == 0) {
            throw new \RuntimeException("Configuration file specified but does not exist");
        }

        $this->write('configurationFile', $found[0]);

        return $this;
    }

    /**
     * Return the path to the Peridot configuration file. Returns a relative
     * path if it exists, otherwise return the provided value
     *
     * @return string
     */
    public function getConfigurationFile()
    {
        return $this->configurationFile;
    }

    /**
     * Set the path to a DSL file for defining
     * the test language used
     *
     * @param string $dsl
     * @return $this
     */
    public function setDsl($dsl)
    {
        return $this->write('dsl', $dsl);
    }

    /**
     * Get the path to a DSL file containing
     * test functions to use
     *
     * @return string
     */
    public function getDsl()
    {
        return $this->dsl;
    }

    /**
     * Write a configuration value and persist it to the current
     * environment.
     *
     * @param $varName
     * @param $value
     * @return $this
     */
    protected function write($varName, $value)
    {
        $this->$varName = $value;
        $parts = preg_split('/(?=[A-Z])/', $varName);
        $env = 'PERIDOT_' . strtoupper(join('_', $parts));
        putenv($env . '=' . $value);
        return $this;
    }
}
