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
     * The "bind" behavior indicates that a scope should
     * bind to a callable.
     */
    const BEHAVIOR_BIND = 1;

    /**
     * The "ignore" behavior indicates that a scope should
     * not bind to a callable
     */
    const BEHAVIOR_IGNORE = 2;

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
     * Lookup properties on child scopes. Since arrays cannot
     * be returned as references in this way, they are returned
     * as ArrayObjects and set on their original scope as an ArrayObject.
     *
     * @param $name
     */
    public function &__get($name)
    {
        list($result, $found, $scope) = $this->peridotLookupScopeProperty($this, $name);
        if ($found) {
            if (is_array($result)) {
                $result = new \ArrayObject($result);
                $scope->$name = $result;
            }
            return $result;
        }
        throw new \DomainException("Scope property $name not found");
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
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
        if (! empty($accumulator)) {
            return $accumulator;
        }

        $children = $scope->peridotGetChildScopes();
        foreach ($children as $childScope) {
            if (method_exists($childScope, $methodName)) {
                $accumulator = [call_user_func_array([$childScope, $methodName], $arguments), true];
            }
            $this->peridotLookupScopeMethod($childScope, $methodName, $arguments, $accumulator);
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
        if (! empty($accumulator)) {
            return $accumulator;
        }

        $children = $scope->peridotGetChildScopes();
        foreach ($children as $childScope) {
            if (property_exists($childScope, $propertyName)) {
                $accumulator = [$childScope->$propertyName, true, $childScope];
            }
            $this->peridotLookupScopeProperty($childScope, $propertyName, $accumulator);
        }
        return $accumulator;
    }
}
