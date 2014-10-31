<?php
namespace Peridot\Console;

use Peridot\Reporter\ReporterFactory;
use Peridot\Runner\Context;
use Peridot\Runner\Runner;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Command\Command as ConsoleCommand;
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
     * @param Environment $environment
     */
    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
        if (! $this->environment->load(getcwd() . DIRECTORY_SEPARATOR . 'peridot.php')) {
            fwrite(STDERR, "Configuration file specified but does not exist" . PHP_EOL);
            exit(1);
        }
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
        if (!is_null($input)) {
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
        $configuration = ConfigurationReader::readInput($input);
        $this->environment->getEventEmitter()->emit('peridot.configure', [$configuration]);

        $runner = new Runner(Context::getInstance()->getCurrentSuite(), $configuration, $this->environment->getEventEmitter());
        $factory = new ReporterFactory($configuration, $output, $this->environment->getEventEmitter());

        $this->loadDsl($configuration->getDsl());
        $this->add(new Command($runner, $configuration, $factory, $this->environment->getEventEmitter()));

        $exitCode = parent::doRun($input, $output);

        $this->environment->getEventEmitter()->emit('peridot.end', [$exitCode, $input, $output]);

        return $exitCode;
    }

    /**
     * {@inheritdoc}
     *
     * This implementation of run will not over write commands
     * in the internal collection if one of the same name already
     * exists
     *
     * @param \Symfony\Component\Console\Command\Command $command
     * @return \Symfony\Component\Console\Command\Command
     */
    public function add(ConsoleCommand $command)
    {
        if ($this->has($command->getName())) {
            return $this->get($command->getName());
        }
        return parent::add($command);
    }


    /**
     * Fetch the ArgvInput used by Peridot. If any exceptions are thrown due to
     * a mismatch between the option or argument requested and the input definition, the
     * exception will be rendered and Peridot will exit with an error code
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
     * Return the peridot input definition defined by Environment
     *
     * @return InputDefinition
     */
    protected function getDefaultInputDefinition()
    {
        return $this->environment->getDefinition();
    }
}
