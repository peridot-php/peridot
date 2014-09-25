<?php
namespace Peridot\Core;
use Evenement\EventEmitterTrait;

/**
 * Class AbstractSpec
 * @package Peridot\Core
 */
abstract class AbstractSpec implements SpecInterface
{
    use EventEmitterTrait;
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
     * @var SpecInterface
     */
    protected $parent;

    /**
     * @var bool|null
     */
    protected $pending = null;

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

    /**
     * @param SpecInterface $parent
     */
    public function setParent(SpecInterface $parent)
    {
        $this->parent = $parent;
    }

    /**
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
    public function isPending()
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
}
