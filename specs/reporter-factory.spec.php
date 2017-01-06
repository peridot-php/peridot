<?php
use Evenement\EventEmitter;
use Peridot\Configuration;
use Peridot\Core\Suite;
use Peridot\Reporter\AnonymousReporter;
use Peridot\Reporter\CompositeReporter;
use Peridot\Reporter\ReporterFactory;
use Peridot\Reporter\SpecReporter;
use Peridot\Runner\Runner;

describe('ReporterFactory', function() {

    beforeEach(function() {
        $configuration = new Configuration();
        $output = new Symfony\Component\Console\Output\NullOutput();
        $this->factory = new ReporterFactory($configuration, $output, new EventEmitter());
    });

    describe('->create()', function() {
        context("using a valid reporter name", function() {
            it("should return an instance of the named reporter", function() {
                $reporter = $this->factory->create('spec');
                assert($reporter instanceof SpecReporter, "should create SpecReporter");
            });

            it("should return an anonymous reporter if callable used", function() {
                $this->factory->register('spec2', 'desc', function($reporter) {});
                $reporter = $this->factory->create('spec2');
                assert($reporter instanceof AnonymousReporter, "should create AnonymousReporter");
            });
        });

        context("using a valid name with an invalid factory", function() {
            it("should throw an exception", function() {
                $this->factory->register('nope', 'doesnt work', 'Not\A\Class');
                $exception = null;
                try {
                    $this->factory->create('nope');
                } catch (RuntimeException $e) {
                    $exception = $e;
                }
                assert(!is_null($exception), 'exception should have been thrown');
            });
        });

        context("using an invalid name", function() {
            it("should throw an exception", function() {
                $exception = null;
                try {
                    $this->factory->create('nope');
                } catch (RuntimeException $e) {
                    $exception = $e;
                }
                assert(!is_null($exception), 'exception should have been thrown');
            });
        });
    });

    describe('->createComposite()', function() {
        context('using valid reporter names', function() {
            it('should return a composite of the named reporters', function() {
                $this->factory->register('spec2', 'desc', function($reporter) {});
                $reporter = $this->factory->createComposite(['spec', 'spec2']);
                $reporters = $reporter->getReporters();
                assert($reporter instanceof CompositeReporter, 'should create CompositeReporter');
                assert($reporters[0] instanceof SpecReporter, 'first reporter should be a SpecReporter');
            });
        });

        context('using valid names with invalid factories', function() {
            it('should throw an exception', function() {
                $this->factory->register('nope', 'doesnt work', 'Not\A\Class');
                $exception = null;
                try {
                    $this->factory->createComposite(['spec', 'nope']);
                } catch (RuntimeException $e) {
                    $exception = $e;
                }
                assert(!is_null($exception), 'exception should have been thrown');
            });
        });

        context('using invalid names', function() {
            it('should throw an exception', function() {
                $exception = null;
                try {
                    $this->factory->createComposite(['spec', 'nope']);
                } catch (RuntimeException $e) {
                    $exception = $e;
                }
                assert(!is_null($exception), 'exception should have been thrown');
            });
        });

        context('using an empty name list', function() {
            it('should throw an exception', function() {
                $exception = null;
                try {
                    $this->factory->createComposite([]);
                } catch (InvalidArgumentException $e) {
                    $exception = $e;
                }
                assert(!is_null($exception), 'exception should have been thrown');
            });
        });
    });

    describe('->getReporters()', function() {
        it("should return an array of reporter information", function() {
            $reporters = $this->factory->getReporters();
            assert(isset($reporters['spec']['description']), 'reporter should have description');
            assert(isset($reporters['spec']['factory']), 'reporter should have factory');
        });
    });

    describe('->register()', function() {
       context('using a class', function() {
          it('should add named reporter to list of reporters', function() {
              $this->factory->register('spec2', 'even speccier', 'Peridot\Reporter\SpecReporter');
              $reporters = $this->factory->getReporters();
              assert(isset($reporters['spec2']['description']), 'reporter should have description');
              assert(isset($reporters['spec2']['factory']), 'reporter should have factory');
          });
       });
    });

});
