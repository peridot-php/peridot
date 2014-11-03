<?php
namespace Peridot\Core;

use BadMethodCallException;
use Closure;
use DomainException;

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
     * Bind a callable to the scope.
     *
     * @param callable $callable
     * @return callable
     */
    public function peridotBindTo(callable $callable)
    {
        return Closure::bind($callable, $this, $this);
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throw BadMethodCallException
     */
    public function __call($name, $arguments)
    {
        list($result, $found) = $this->peridotScanChildren($this, function ($childScope, &$accumulator) use ($name, $arguments) {
            if (method_exists($childScope, $name)) {
                $accumulator = [call_user_func_array([$childScope, $name], $arguments), true];
            }
        });
        if (!$found) {
            throw new BadMethodCallException("Scope method $name not found");
        }
        return $result;
    }

    /**
     * Lookup properties on child scopes.
     *
     * @param $name
     * @return mixed
     * @throws DomainException
     */
    public function &__get($name)
    {
        list($result, $found) = $this->peridotScanChildren($this, function ($childScope, &$accumulator) use ($name) {
            if (property_exists($childScope, $name)) {
                $accumulator = [$childScope->$name, true, $childScope];
            }
        });
        if (!$found) {
            throw new DomainException("Scope property $name not found");
        }
        return $result;
    }

    /**
     * Scan child scopes and execute a function against each one passing an
     * accumulator reference along.
     *
     * @param Scope $scope
     * @param callable $fn
     * @param array $accumulator
     * @return array
     */
    protected function peridotScanChildren(Scope $scope, callable $fn, &$accumulator = [])
    {
        if (! empty($accumulator)) {
            return $accumulator;
        }

        $children = $scope->peridotGetChildScopes();
        foreach ($children as $childScope) {
            $fn($childScope, $accumulator);
            $this->peridotScanChildren($childScope, $fn, $accumulator);
        }
        return $accumulator;
    }
}
