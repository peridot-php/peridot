<?php
namespace Peridot\Console;

use Peridot\Reporter\ReporterFactory;
use Peridot\Runner\Context;
use Peridot\Runner\Runner;
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
     * @param Environment $environment
     */
    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
        if (! $this->environment->load(getcwd() . DIRECTORY_SEPARATOR . 'peridot.php')) {
            fwrite(STDERR, "Configuration file specified but does not exist" . PHP_EOL);
            exit(1);
        }
        $this->environment->getEventEmitter()->emit('peridot.start', [$this->environment->getDefinition()]);
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
        $in = null;
        if (!is_null($input)) {
            $in = $input;
        } else {
            $in = $this->getInput();
        }

        return parent::run($in, $output);
    }

    /**
     * Run the peridot application
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $configuration = ConfigurationReader::readInput($input);

        if (file_exists($configuration->getDsl())) {
            include $configuration->getDsl();
        }

        $runner = new Runner(Context::getInstance()->getCurrentSuite(), $configuration, $this->environment->getEventEmitter());
        $factory = new ReporterFactory($configuration, $output, $this->environment->getEventEmitter());

        $this->add(new Command($runner, $configuration, $factory, $this->environment->getEventEmitter()));

        return parent::doRun($input, $output);
    }

    /**
     * Fetch the ArgvInput used by Peridot. If any exceptions are thrown due to
     * a mismatch between the option or argument requested and the input definition, the
     * exception will be rendered and Peridot will exit with an error code
     *
     * @return ArgvInput
     */
    public function getInput()
    {
        try {
            return new ArgvInput(null, $this->environment->getDefinition());
        } catch (\Exception $e) {
            $this->renderException($e, new ConsoleOutput());
            exit(1);
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
}
