<?php
namespace Peridot\Core;
use Evenement\EventEmitterInterface;

/**
 * Interface SpecInterface
 * @package Peridot\Core
 */
interface SpecInterface extends EventEmitterInterface
{
    /**
     * @param SpecResult $result
     * @return mixed
     */
    public function run(SpecResult $result);

    /**
     * Add a function to execute before the spec runs
     *
     * @param callable $setupFn
     */
    public function addSetUpFunction(callable $setupFn);

    /**
     * Add a function to execute after the spec runs
     *
     * @param callable $tearDownFn
     */
    public function addTearDownFunction(callable $tearDownFn);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * Returns the callable definition of the SpecInterface
     *
     * @return callable
     */
    public function getDefinition();

    /**
     * @return SpecInterface
     */
    public function getParent();

    /**
     * @param SpecInterface $parent
     * @return mixed
     */
    public function setParent(SpecInterface $parent);

    /**
     * Returns the full description including parent descriptions
     *
     * @return string
     */
    public function getTitle();

    /**
     * Return whether or not the spec is pending
     *
     * @return bool|null
     */
    public function isPending();

    /**
     * Set the pending status of the spec
     *
     * @param bool $state
     * @return void
     */
    public function setPending($state);
} 
