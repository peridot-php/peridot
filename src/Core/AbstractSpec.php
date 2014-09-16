<?php
namespace Peridot\Core;

/**
 * Class AbstractSpec
 * @package Peridot\Core
 */
abstract class AbstractSpec implements SpecInterface
{
    /**
     * The spec definition as a callable
     *
     * @var callable
     */
    protected $definition;

    /**
     * A collection of functions to run
     * before specs execute
     *
     * @var array
     */
    protected $setUpFns = [];

    /**
     * A collection of functions to run
     * after specs execute
     *
     * @var array
     */
    protected $tearDownFns = [];

    /**
     * @var string
     */
    protected $description;

    /**
     * Constructor
     *
     * @param string $description
     * @param callable $definition
     */
    public function __construct($description, callable $definition)
    {

        $this->definition = $definition;
        $this->description = $description;
    }

    /**
     * {@inheritdoc}
     *
     * @param callable $setupFn
     */
    public function addSetUpFunction(callable $setupFn)
    {
        $this->setUpFns[] = \Closure::bind($setupFn, $this, $this);
    }

    /**
     * {@inheritdoc}
     *
     * @param callable $tearDownFn
     */
    public function addTearDownFunction(callable $tearDownFn)
    {
        $this->tearDownFns[] = \Closure::bind($tearDownFn, $this, $this);
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     *
     * @return callable
     */
    public function getDefinition()
    {
        return $this->definition;
    }
}