<?php
namespace Peridot\Core;

use Closure;

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
     */
    public function __call($name, $arguments)
    {
        list($result, $found) = $this->peridotScanChildren($this, function ($childScope, &$accumulator) use ($name, $arguments) {
            if (method_exists($childScope, $name)) {
                $accumulator = [call_user_func_array([$childScope, $name], $arguments), true];
            }
        });
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
        list($result, $found, $scope) = $this->peridotScanChildren($this, function ($childScope, &$accumulator) use ($name) {
            if (property_exists($childScope, $name)) {
                $accumulator = [$childScope->$name, true, $childScope];
            }
        });
        if ($found) {
            if (is_array($result)) {
                $result = new \ArrayObject($result);
                $scope->$name = $result;
            }
            return $result;
        }
        throw new \DomainException("Scope property $name not found");
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
