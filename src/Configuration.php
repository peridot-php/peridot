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
     * @var boolean
     */
    protected $colorsEnableExplicit = false;

    /**
     * @var string|null
     */
    protected $focusPattern;

    /**
     * @var string|null
     */
    protected $skipPattern;

    /**
     * @var string
     */
    protected $grep = '*.spec.php';

    /**
     * @var array
     */
    protected $reporters = ['spec'];

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
     * Set the pattern used to focus tests
     *
     * @param string|null $pattern
     * @return $this
     */
    public function setFocusPattern($pattern)
    {
        return $this->write('focusPattern', $this->normalizeRegexPattern($pattern));
    }

    /**
     * Returns the pattern used to focus tests
     *
     * @return string|null
     */
    public function getFocusPattern()
    {
        return $this->focusPattern;
    }

    /**
     * Set the pattern used to skip tests
     *
     * @param string|null $pattern
     * @return $this
     */
    public function setSkipPattern($pattern)
    {
        return $this->write('skipPattern', $this->normalizeRegexPattern($pattern));
    }

    /**
     * Returns the pattern used to skip tests
     *
     * @return string|null
     */
    public function getSkipPattern()
    {
        return $this->skipPattern;
    }

    /**
     * Set the name of the reporter to use
     *
     * @param string $reporter
     * @return $this
     */
    public function setReporter($reporter)
    {
        return $this->writeReporters([$reporter]);
    }

    /**
     * Return the name of the reporter configured for use
     *
     * @return string
     */
    public function getReporter()
    {
        return $this->reporters[0];
    }

    /**
     * Set the names of the reporters to use
     *
     * @param array $reporters
     * @return $this
     */
    public function setReporters(array $reporters)
    {
        if (empty($reporters)) {
            throw new \InvalidArgumentException('Reporters cannot be empty.');
        }

        return $this->writeReporters($reporters);
    }

    /**
     * Return the names of the reporters configured for use
     *
     * @return array
     */
    public function getReporters()
    {
        return $this->reporters;
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
        if ( $this->colorsEnableExplicit ) {
            return $this;
        }

        return $this->write('colorsEnabled', false);
    }

    /**
     * Force output colors even without TTY support.
     *
     * @return $this
     */
    public function enableColorsExplicit()
    {
        return $this
            ->write('colorsEnableExplicit', true)
            ->write('colorsEnabled', true);
    }

    /**
     * Check if output colors are disabled
     *
     * @return boolean
     */
    public function areColorsEnabled()
    {
        return $this->colorsEnableExplicit || $this->colorsEnabled;
    }

    /**
     * Check if output colors are explicitly enabled.
     *
     * @return boolean
     */
    public function areColorsEnabledExplicit()
    {
        return $this->colorsEnableExplicit;
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

    /**
     * Normalize the supplied regular expression pattern.
     *
     * @param string $pattern
     * @return string
     */
    protected function normalizeRegexPattern($pattern)
    {
        if (false !== @preg_match($pattern, null)) {
            return $pattern;
        }

        $boundedPattern = '~\b' . str_replace('~', '\~', $pattern) . '\b~';

        if (false !== @preg_match($boundedPattern, null)) {
            return $boundedPattern;
        }

        return '~\b' . preg_quote($pattern, '~') . '\b~';
    }

    /**
     * Write the reporters and persist them to the current environment.
     *
     * @param array $reporters
     * @return $this
     */
    protected function writeReporters(array $reporters)
    {
        $this->reporters = $reporters;
        putenv('PERIDOT_REPORTER=' . $reporters[0]);
        putenv('PERIDOT_REPORTERS=' . implode(',', $reporters));
        return $this;
    }
}
