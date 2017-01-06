<?php
namespace Peridot\Reporter;

use Evenement\EventEmitterInterface;
use Peridot\Configuration;
use Peridot\Core\HasEventEmitterTrait;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The ReporterFactory is used to list and register Peridot reporters.
 *
 * @package Peridot\Reporter
 */
class ReporterFactory
{
    use HasEventEmitterTrait;

    /**
     * @var \Peridot\Configuration
     */
    protected $configuration;

    /**
     * @var \Peridot\Runner\Runner
     */
    protected $runner;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * Registered reporters
     *
     * @var array
     */
    protected $reporters = array(
        'spec' => ['description' => 'hierarchical spec list', 'factory' => 'Peridot\Reporter\SpecReporter']
    );

    /**
     * @param Configuration $configuration
     * @param OutputInterface $output
     * @param EventEmitterInterface $eventEmitter
     */
    public function __construct(
        Configuration $configuration,
        OutputInterface $output,
        EventEmitterInterface $eventEmitter
    ) {
        $this->configuration = $configuration;
        $this->output = $output;
        $this->eventEmitter = $eventEmitter;
    }

    /**
     * Return an instance of the named reporter
     *
     * @param $name
     * @return \Peridot\Reporter\AbstractBaseReporter
     */
    public function create($name)
    {
        return $this->createWithOutput($this->output, $name);
    }

    /**
     * Return an instance of the named reporter
     *
     * @param $name
     * @return \Peridot\Reporter\AbstractBaseReporter
     */
    public function createComposite(array $names)
    {
        if (empty($names)) {
            throw new \InvalidArgumentException('Reporter names cannot be empty.');
        }

        return new CompositeReporter(
            array_merge(
                [$this->createWithOutput($this->output, array_shift($names))],
                array_map(function ($name) {
                    return $this->createWithOutput(new BufferedOutput(), $name);
                }, $names)
            ),
            $this->configuration,
            $this->output,
            $this->eventEmitter
        );
    }

    /**
     * Return the factory defined for the named reporter
     *
     * @param string $name
     * @return null|string|callable
     */
    public function getReporterFactory($name)
    {
        $definition = $this->getReporterDefinition($name);
        if (! isset($definition['factory'])) {
            $definition['factory'] = null;
        }
        return $definition['factory'];
    }

    /**
     * Return the definition of the named reporter
     *
     * @param string $name
     * @return array
     */
    public function getReporterDefinition($name)
    {
        $definition = [];
        if (isset($this->reporters[$name])) {
            $definition = $this->reporters[$name];
        }
        return $definition;
    }

    /**
     * Register a named reporter with the factory.
     *
     * @param string $name
     * @param string $description
     * @param string $factory Either a callable or a fully qualified class name
     */
    public function register($name, $description, $factory)
    {
        $this->reporters[$name] = ['description' => $description, 'factory' => $factory];
    }

    /**
     * @return array
     */
    public function getReporters()
    {
        return $this->reporters;
    }

    private function createWithOutput(OutputInterface $output, $name)
    {
        $factory = $this->getReporterFactory($name);
        $isClass = is_string($factory) && class_exists($factory);

        if ($isClass) {
            return new $factory($this->configuration, $output, $this->eventEmitter);
        }

        if (is_callable($factory)) {
            return new AnonymousReporter($factory, $this->configuration, $output, $this->eventEmitter);
        }

        throw new \RuntimeException("Reporter class could not be created");
    }
}
