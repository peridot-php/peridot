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
