<?php
namespace Peridot\Console;

use Peridot\Configuration;
use Peridot\Reporter\ReporterFactory;
use Peridot\Runner\Context;
use Peridot\Runner\Runner;
use Peridot\Runner\RunnerInterface;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The main Peridot application class.
 *
 * @package Peridot\Console
 */
class Application extends ConsoleApplication
{
    /**
     * @var Environment
     */
    protected $environment;

    /**
     * @var RunnerInterface
     */
    protected $runner;

    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @param Environment $environment
     */
    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
        $this->validateConfiguration();
        $this->environment->getEventEmitter()->emit('peridot.start', [$this->environment, $this]);
        parent::__construct(Version::NAME, Version::NUMBER);
    }

    /**
     * {@inheritdoc}
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return int
     */
    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        if ($input !== null) {
            $in = $input;
        } else {
            $in = $this->getInput();
        }

        return parent::run($in, $output);
    }

    /**
     * Run the Peridot application
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $this->configuration = ConfigurationReader::readInput($input);
        $this->environment->getEventEmitter()->emit('peridot.configure', [$this->configuration, $this]);

        $runner = $this->getRunner();
        $factory = new ReporterFactory($this->configuration, $output, $this->environment->getEventEmitter());

        $this->loadDsl($this->configuration->getDsl());
        $this->add(new Command($runner, $this->configuration, $factory, $this->environment->getEventEmitter()));

        $exitCode = parent::doRun($input, $output);

        $this->environment->getEventEmitter()->emit('peridot.end', [$exitCode, $input, $output]);

        return $exitCode;
    }

    /**
     * Fetch the ArgvInput used by Peridot. If any exceptions are thrown due to
     * a mismatch between the option or argument requested and the input definition, the
     * exception will be rendered and Peridot will exit with an error code.
     *
     * @param array $argv An array of parameters from the CLI in the argv format.
     * @return ArgvInput
     */
    public function getInput(array $argv = null)
    {
        try {
            return new ArgvInput($argv, $this->environment->getDefinition());
        } catch (\Exception $e) {
            $this->renderException($e, new ConsoleOutput());
            exit(1);
        }
    }

    /**
     * Return's peridot as the sole command used by Peridot
     *
     * @param  InputInterface $input
     * @return string
     */
    public function getCommandName(InputInterface $input)
    {
        return 'peridot';
    }

    /**
     * Load the configured DSL.
     *
     * @param $dsl
     */
    public function loadDsl($dslPath)
    {
        if (file_exists($dslPath)) {
            include_once $dslPath;
        }
    }

    /**
     * Set the runner used by the Peridot application.
     *
     * @param RunnerInterface $runner
     * @return $this
     */
    public function setRunner(RunnerInterface $runner)
    {
        $this->runner = $runner;
        return $this;
    }

    /**
     * Get the RunnerInterface being used by the Peridot application.
     * If one is not set, a default Runner will be used.
     *
     * @return RunnerInterface
     */
    public function getRunner()
    {
        if ($this->runner === null) {
            $this->runner = new Runner(
                Context::getInstance()->getCurrentSuite(),
                $this->getConfiguration(),
                $this->environment->getEventEmitter()
            );
        }
        return $this->runner;
    }

    /**
     * Return the Environment used by the Peridot application.
     *
     * @return Environment
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * Return the configuration used by the Peridot application.
     *
     * @return Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Set the configuration object used by the Peridot application.
     *
     * @param Configuration $configuration
     * @return $this
     */
    public function setConfiguration(Configuration $configuration)
    {
        $this->configuration = $configuration;
        return $this;
    }

    /**
     * Return the peridot input definition defined by Environment
     *
     * @return InputDefinition
     */
    protected function getDefaultInputDefinition()
    {
        return $this->environment->getDefinition();
    }

    /**
     * Validate that a supplied configuration exists.
     *
     * @return void
     */
    protected function validateConfiguration()
    {
        if (!$this->environment->load(getcwd() . DIRECTORY_SEPARATOR . 'peridot.php')) {
            fwrite(STDERR, "Configuration file specified but does not exist" . PHP_EOL);
            exit(1);
        }
    }
}
