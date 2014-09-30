<?php
namespace Peridot;

/**
 * Class Configuration
 * @package Peridot
 */
class Configuration 
{
    /**
     * @var string
     */
    protected $grep = '*.spec.php';

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
} 
