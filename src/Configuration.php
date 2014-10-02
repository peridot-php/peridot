<?php
namespace Peridot;

/**
 * Class Configuration
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
     * @var bool
     */
    protected $stopOnFailure = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->path = getcwd();
    }

    /**
     * Set the pattern used to load specs
     *
     * @param string $grep
     */
    public function setGrep($grep)
    {
        $this->grep = $grep;
        return $this;
    }

    /**
     * Returns the pattern used to load specs
     *
     * @return string
     */
    public function getGrep()
    {
        return $this->grep;
    }

    /**
     * @param string $reporter
     */
    public function setReporter($reporter)
    {
        $this->reporter = $reporter;
        return $this;
    }

    /**
     * @return string
     */
    public function getReporter()
    {
        return $this->reporter;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return $this
     */
    public function disableColors()
    {
        $this->colorsEnabled = false;
        return $this;
    }

    /**
     * @return boolean
     */
    public function areColorsEnabled()
    {
        return $this->colorsEnabled;
    }

    /**
     * @return void
     */
    public function stopOnFailure()
    {
        $this->stopOnFailure = true;
    }

    /**
     * @return bool
     */
    public function shouldStopOnFailure()
    {
        return $this->stopOnFailure;
    }
} 
