<?php
namespace Some\Really\Long\Name\That\Looks\Dumb;

describe('Exception trimming', function () {
    it('should not display irrelevent stack frames', function () {
        new SomeClass();
    });

    it('should render assertion exceptions', function () {
        assert('true === false', 'should implode the universe');
    });
});

class SomeClass
{
    public function __construct()
    {
        someFunction();
    }
}

function someFunction()
{
    new SomeOtherClass();
}

class SomeOtherClass
{
    public function __construct()
    {
        throw new \Exception('You done goofed.');
    }
}
