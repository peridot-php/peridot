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
     * @return array
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
        foreach ($this->peridotChildScopes as $scope) {
            if (property_exists($scope, $name)) {
                return $scope->$name;
            }
        }
        throw new \DomainException("Scope property $name not found");
    }

    /**
     * Return a method result or null
     *
     * @param Scope $scope
     * @param $methodName
     * @param $arguments
     * @return mixed|null
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
}
