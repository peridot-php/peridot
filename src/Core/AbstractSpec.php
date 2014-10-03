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
     * This is oddly named so using it will only be VERY intentional
     *
     * @var Scope
     */
    protected $________peridotScope;

    /**
     * Constructor.
     *
     * @param string $description
     * @param callable $definition
     */
    public function __construct($description, callable $definition)
    {
        $this->________peridotScope = new Scope();
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
        $this->setUpFns[] = Closure::bind(
            $setupFn,
            $this->________peridotScope,
            $this->________peridotScope
        );
    }

    /**
     * {@inheritdoc}
     *
     * @param callable $tearDownFn
     */
    public function addTearDownFunction(callable $tearDownFn)
    {
        $this->tearDownFns[] = Closure::bind(
            $tearDownFn,
            $this->________peridotScope,
            $this->________peridotScope
        );
    }

    /**
     * {@inheritdoc}
     *
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

    /**
     * {@inheritdoc}
     *
     * @param SpecInterface $parent
     * @return mixed|void
     */
    public function setParent(SpecInterface $parent)
    {
        $this->parent = $parent;
    }

    /**
     * {@inheritdoc}
     *
     * @return SpecInterface
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
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
     *
     * @return bool|null
     */
    public function getPending()
    {
        return $this->pending;
    }

    /**
     * {@inheritdoc}
     *
     * @param bool $state
     */
    public function setPending($state)
    {
        $this->pending = (bool)$state;
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function getSetUpFunctions()
    {
        return $this->setUpFns;
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function getTearDownFunctions()
    {
        return $this->tearDownFns;
    }

    /**
     * {@inheritdoc}
     *
     * @return \Peridot\Core\Scope
     */
    public function getScope()
    {
        return $this->________peridotScope;
    }
}
