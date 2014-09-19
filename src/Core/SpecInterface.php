<?php
namespace Peridot\Core;

/**
 * Interface SpecInterface
 * @package Peridot\Core
 */
interface SpecInterface
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
} 
