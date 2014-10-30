<?php

namespace Peridot\Core;

use Closure;

/**
 * Base class for Peridot Suites and Tests
 *
 * @package Peridot\Core
 */
abstract class AbstractTest implements TestInterface
{
    use HasEventEmitterTrait;

    /**
     * The test definition as a callable.
     *
     * @var callable
     */
    protected $definition;

    /**
     * A collection of functions to run before tests execute.
     *
     * @var array
     */
    protected $setUpFns = [];

    /**
     * A collection of functions to run after tests execute.
     *
     * @var array
     */
    protected $tearDownFns = [];

    /**
     * @var string
     */
    protected $description;

    /**
     * @var TestInterface
     */
    protected $parent;

    /**
     * @var bool|null
     */
    protected $pending = null;

    /**
     *
     * @var Scope
     */
    protected $scope;

    /**
     * @param string   $description
     * @param callable $definition
     */
    public function __construct($description, callable $definition)
    {
        $this->definition = $definition;
        $this->description = $description;
        $this->scope = new Scope();
    }

    /**
     * {@inheritdoc}
     *
     * @param callable $setupFn
     */
    public function addSetupFunction(callable $setupFn)
    {
        array_unshift($this->setUpFns, Closure::bind(
            $setupFn,
            $this->scope,
            $this->scope
        ));
    }

    /**
     * {@inheritdoc}
     *
     * @param callable $tearDownFn
     */
    public function addTearDownFunction(callable $tearDownFn)
    {
        array_unshift($this->tearDownFns, Closure::bind(
            $tearDownFn,
            $this->scope,
            $this->scope
        ));
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
        $boundDefinition = Closure::bind(
            $this->definition,
            $this->scope,
            $this->scope
        );
        return $boundDefinition;
    }

    /**
     * {@inheritdoc}
     *
     * @param  TestInterface $parent
     * @return mixed|void
     */
    public function setParent(TestInterface $parent)
    {
        $this->parent = $parent;
    }

    /**
     * {@inheritdoc}
     *
     * @return TestInterface
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

        return implode(' ', $parts);
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
        $this->pending = (bool) $state;
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function getSetupFunctions()
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
        return $this->scope;
    }

    /**
     * {@inheritdoc}
     *
     * @param Scope $scope
     * @return $this
     */
    public function setScope(Scope $scope)
    {
        $this->scope = $scope;
        return $this;
    }
}
