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
        return $this->peridotLookupScopeMethod($this, $name, $arguments);
    }

    /**
     * @param $name
     */
    public function __get($name)
    {
        return $this->peridotLookupScopeProperty($this, $name);
    }

    /**
     * Return a method result by searching against a scope and
     * all of its children
     *
     * @param Scope $scope
     * @param $methodName
     * @param $arguments
     * @return mixed
     * @throws \BadMethodCallException
     */
    protected function peridotLookupScopeMethod(Scope $scope, $methodName, $arguments)
    {
        $children = $scope->peridotGetChildScopes();
        foreach ($children as $childScope) {
            if (method_exists($childScope, $methodName)) {
                return call_user_func_array([$childScope, $methodName], $arguments);
            }
            return $this->peridotLookupScopeMethod($childScope, $methodName, $arguments);
        }
        throw new \BadMethodCallException("Scope method $methodName not found");
    }

    /**
     * Return a property by searching against a scope and
     * all of its children
     *
     * @param Scope $scope
     * @param $propertyName
     * @return mixed
     * @throws \DomainException
     */
    protected function peridotLookupScopeProperty(Scope $scope, $propertyName)
    {
        $children = $scope->peridotGetChildScopes();
        foreach ($children as $childScope) {
            if (property_exists($childScope, $propertyName)) {
                return $childScope->$propertyName;
            }
            return $this->peridotLookupScopeProperty($childScope, $propertyName);
        }
        throw new \DomainException("Scope property $propertyName not found");
    }
}
