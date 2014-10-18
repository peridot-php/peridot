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
     * @var array
     */
    protected $peridotChildScopes = [];

    /**
     * @param Scope $scope
     */
    public function peridotAddChildScope(Scope $scope)
    {
        $this->peridotChildScopes[] = $scope;
    }

    /**
     * @return array
     */
    public function peridotGetChildScopes()
    {
        return $this->peridotChildScopes;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        foreach ($this->peridotChildScopes as $scope) {
            if (method_exists($scope, $name)) {
                return call_user_func_array([$scope, $name], $arguments);
            }
        }
        throw new \BadMethodCallException("Scope method $name not found");
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


}
