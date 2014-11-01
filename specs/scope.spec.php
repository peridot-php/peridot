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

            context("when mixing in multiple scopes, one of which has a child", function() {
                it ("should look up the child scope on the sibling", function() {
                    $testScope = new TestScope();
                    $testSibling = new TestSiblingScope();
                    $testChild = new TestChildScope();
                    $testSibling->peridotAddChildScope($testChild);
                    $this->scope->peridotAddChildScope($testScope);
                    $this->scope->peridotAddChildScope($testSibling);

                    $number = $this->scope->getNumber();
                    $evenNumber = $this->scope->getEvenNumber();
                    $oddNumber = $this->scope->getOddNumber();

                    assert($number === 5, "expected result of TestScope::getNumber()");
                    assert($evenNumber === 4, "expected result of TestChildScope::getEvenNumber()");
                    assert($oddNumber === 3, "expected result of TestSiblingScope::getOddNumber()");
                });
            });
        });

        context("when mixing in multiple scopes", function() {
            it ("should look up methods for sibling scopes", function() {
                $this->scope->peridotAddChildScope(new TestScope());
                $this->scope->peridotAddChildScope(new TestChildScope());
                $evenNumber = $this->scope->getEvenNumber();
                $number = $this->scope->getNumber();
                assert($evenNumber === 4, "expected scope to look up child method getEvenNumber()");
                assert($number === 5, "expected scope to look up child method getNumber()");
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

        it('should return an ArrayObject for an array', function() {
            $this->scope->peridotAddChildScope(new TestScope());
            $data = $this->scope->data;
            assert($data instanceof ArrayObject, "data should be array object");
            assert($data['one'] == 1, "should be able to access as array");
        });

        context("and the desired property is on a child scope's child", function() {
            it ("should look up property on the child scope's child", function() {
                $testScope = new TestScope();
                $testScope->peridotAddChildScope(new TestChildScope());
                $this->scope->peridotAddChildScope($testScope);
                $surname = $this->scope->surname;
                assert($surname === "scaturro", "expected scope to look up child scope's child property");
            });

            context("when mixing in multiple scopes, one of which has a child", function() {
                it ("should look up the child scope on the sibling", function() {
                    $testScope = new TestScope();
                    $testSibling = new TestSiblingScope();
                    $testChild = new TestChildScope();
                    $testSibling->peridotAddChildScope($testChild);
                    $this->scope->peridotAddChildScope($testScope);
                    $this->scope->peridotAddChildScope($testSibling);

                    $name = $this->scope->name;
                    $middle = $this->scope->middleName;
                    $surname = $this->scope->surname;

                    assert($name === "brian", "expected result of TestScope::name");
                    assert($middle == "zooooom", "expected result of TestSiblingScope::middleName");
                    assert($surname === "scaturro", "expected result of TestChildScope::surname");
                });
            });
        });

        context("when mixing in multiple scopes", function() {
            it ("should look up properties for sibling scopes", function() {
                $this->scope->peridotAddChildScope(new TestScope());
                $this->scope->peridotAddChildScope(new TestChildScope());
                $name = $this->scope->name;
                $surname = $this->scope->surname;
                assert($name === "brian", "expected result of TestScope::name");
                assert($surname === "scaturro", "expected result of TestChildScope::surname");
            });
        });
    });
});

class TestScope extends Scope
{
    public $name = "brian";

    public $data;

    public function __construct()
    {
        $this->data = ['one' => 1, 'two' => 2];
    }

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

class TestSiblingScope extends Scope
{
    public $middleName = "zooooom";

    public function getOddNumber()
    {
        return 3;
    }
}
