<?php
use Peridot\Core\Scope;

describe('Scope', function() {
    beforeEach(function() {
        $this->scope = new Scope();
    });

    describe('->peridotAddChildScope()', function() {
        it('should mixin behavior via __call', function() {
            $this->scope->peridotAddChildScope(new TestScope());
            $number = $this->scope->getNumber();
            assert(5 === $number, 'getNumber() should return value');
        });

        it('should mixin properties via __get', function() {
            $this->scope->peridotAddChildScope(new TestScope());
            $name = $this->scope->name;
            assert($name == "brian", "property should return value");
        });
    });

    context("when calling a mixed in method", function() {
        it('should throw an exception when method not found', function() {
            $exception = null;
            try {
                $this->scope->nope();
            } catch (\Exception $e) {
                $exception = $e;
            }
            assert(!is_null($exception), 'exception should not be null');
        });

        context("and the desired method is on a child scope's child", function() {
            it ("should look up method on the child scope's child", function() {
                $testScope = new TestScope();
                $testScope->peridotAddChildScope(new TestChildScope());
                $this->scope->peridotAddChildScope($testScope);
                $evenNumber = $this->scope->getEvenNumber();
                assert($evenNumber === 4, "expected scope to look up child scope's child method");
            });
        });
    });

    context('when calling a mixed in property', function() {
        it('should throw an exception when property not found', function() {
            $exception = null;
            try {
                $this->scope->nope;
            } catch (\Exception $e) {
                $exception = $e;
            }
            assert(!is_null($exception), 'exception should not be null');
        });

        context("and the desired property is on a child scope's child", function() {
            it ("should look up property on the child scope's child", function() {
                $testScope = new TestScope();
                $testScope->peridotAddChildScope(new TestChildScope());
                $this->scope->peridotAddChildScope($testScope);
                $surname = $this->scope->surname;
                assert($surname === "scaturro", "expected scope to look up child scope's child property");
            });
        });
    });
});

class TestScope extends Scope
{
    public $name = "brian";

    public function getNumber()
    {
        return 5;
    }
}

class TestChildScope extends Scope
{
    public $surname = "scaturro";

    public function getEvenNumber()
    {
        return 4;
    }
}
