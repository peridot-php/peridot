<?php
namespace Peridot\Core;

/**
 * Property bag for scoping tests "instance variables". This exists to
 * prevent tests from creating instance variable collisions.
 *
 * @package Peridot\Core
 */
class Scope
{
    /**
     * @var \SplObjectStorage
     */
    protected $peridotChildScopes;

    /**
     * @param Scope $scope
     */
    public function peridotAddChildScope(Scope $scope)
    {
        $this->peridotGetChildScopes()->attach($scope);
    }

    /**
     * @return \SplObjectStorage
     */
    public function peridotGetChildScopes()
    {
        if (!isset($this->peridotChildScopes)) {
            $this->peridotChildScopes = new \SplObjectStorage();
        }
        return $this->peridotChildScopes;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        list($result, $found) = $this->peridotLookupScopeMethod($this, $name, $arguments);
        if ($found) {
            return $result;
        }
        throw new \BadMethodCallException("Scope method $name not found");
    }

    /**
     * @param $name
     */
    public function __get($name)
    {
        list($result, $found) = $this->peridotLookupScopeProperty($this, $name);
        if ($found) {
            return $result;
        }
        throw new \DomainException("Scope property $name not found");
    }

    /**
     * Return a method result by searching against a scope and
     * all of its children
     *
     * @param Scope $scope
     * @param $methodName
     * @param $arguments
     * @param $accumulator
     * @return array index 0 is the result and index 1 is whether the method was found
     */
    protected function peridotLookupScopeMethod(Scope $scope, $methodName, $arguments, &$accumulator = [])
    {
        $children = $scope->peridotGetChildScopes();
        foreach ($children as $childScope) {
            if (method_exists($childScope, $methodName)) {
                return [call_user_func_array([$childScope, $methodName], $arguments), true];
            }
            $accumulator = $this->peridotLookupScopeMethod($childScope, $methodName, $arguments);
        }
        return $accumulator;
    }

    /**
     * Return a property by searching against a scope and
     * all of its children
     *
     * @param Scope $scope
     * @param $propertyName
     * @return array index 0 is the result and index 1 is whether the property was found
     */
    protected function peridotLookupScopeProperty(Scope $scope, $propertyName, &$accumulator = [])
    {
        $children = $scope->peridotGetChildScopes();
        foreach ($children as $childScope) {
            if (property_exists($childScope, $propertyName)) {
                return [$childScope->$propertyName, true];
            }
            $accumulator = $this->peridotLookupScopeProperty($childScope, $propertyName);
        }
        return $accumulator;
    }
}
