<?php

namespace Peridot\Core;

use Closure;
use Evenement\EventEmitterTrait;

/**
 * Class AbstractSpec
 * @package Peridot\Core
 */
abstract class AbstractSpec implements SpecInterface
{
    use EventEmitterTrait;

    /**
     * The spec definition as a callable.
     *
     * @var callable
     */
    protected $definition;

    /**
     * A collection of functions to run before specs execute.
     *
     * @var array
     */
    protected $setUpFns = [];

    /**
     * A collection of functions to run after specs execute.
     *
     * @var array
     */
    protected $tearDownFns = [];

    /**
     * @var string
     */
    protected $description;

    /**
     * @var SpecInterface
     */
    protected $parent;

    /**
     * @var bool|null
     */
    protected $pending = null;

    /**
     * Constructor.
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
     */
    public function addSetUpFunction(callable $setupFn)
    {
        $this->setUpFns[] = Closure::bind($setupFn, $this, $this);
    }

    /**
     * {@inheritdoc}
     */
    public function addTearDownFunction(callable $tearDownFn)
    {
        $this->tearDownFns[] = Closure::bind($tearDownFn, $this, $this);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * {@inheritdoc}
     */
    public function setParent(SpecInterface $parent)
    {
        $this->parent = $parent;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        $parts = [];
        $node = $this;
        while ($node != null) {
            array_unshift($parts, $node->getDescription());
            $node = $node->getParent();
        }
        return implode(' ' ,$parts);
    }

    /**
     * {@inheritdoc}
     */
    public function getPending()
    {
        return $this->pending;
    }

    /**
     * {@inheritdoc}
     */
    public function setPending($state)
    {
        $this->pending = (bool)$state;
    }

    /**
     * {@inheritdoc}
     */
    public function getSetUpFunctions()
    {
        return $this->setUpFns;
    }

    /**
     * {@inheritdoc}
     */
    public function getTearDownFunctions()
    {
        return $this->tearDownFns;
    }
}
