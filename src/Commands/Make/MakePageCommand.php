<?php


namespace KevinJansen\SilverstripeArtisan\Console\Commands\Make;


use KevinJansen\SilverstripeArtisan\Console\Framework\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MakePageCommand extends GeneratorCommand
{
    protected $name = "make:page";
    protected $description = "Generate a new Silverstripe page";

    /**
     * Configure this command
     */
    protected function configure()
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'Sets the name of the new Silverstripe page')
            ->addArgument('namespace', InputOption::VALUE_OPTIONAL, 'Sets the namespace for the new page')
            ->addOption('force', 'force', InputOption::VALUE_NONE, 'Overwrite if page already exists');
    }

    /**
     * Executes this command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // First, let's validate that we're working in a SilverStripe project
        $this->validateSilverstripe();

        // Next, let's validate the name for the Page. If it doesn't contain the word "Page" append it.
        $name = $this->validatePageName($input);

        // Next, Let's generate an absolute path and validate that it doesn't already exists.
        $path = SILVERSTRIPE_APP_SRC . '/' . $name;

        // If OS is Windows, swap around the slashes
        if (PHP_OS_FAMILY == 'Windows') {
            $path = str_replace("/", "\\", $path);
        }

        if (!$input->getOption('force')) {
            if ($this->isDirectory($path)) {
                $output->write("<fg=red>Path already exists! Exiting.</>");
                return 1;
            }
        }

        // Next, let's generate the directory and start replacing the Stub files
        // after which we write them to the filesystem
        $this->generateDirectory(SILVERSTRIPE_APP_SRC . '/' . $name);

        $stubs = ['page' => $this->getStub(__DIR__ . '/stubs/page.stub'), 'controller' => $this->getStub(__DIR__ . '/stubs/page.controller.stub')];

        file_put_contents($path . '/' . $name . '.php', $this->replaceClass($stubs['page'], $name));
        file_put_contents($path . '/' . $name . 'Controller.php', $this->replaceClass($stubs['controller'], $name . 'Controller'));

        $output->write("<fg=green>$name created successfully!</>");
        return 0;
    }

    /**
     * Validates the given page name
     *
     * @param InputInterface $input
     * @return string
     */
    private function validatePageName(InputInterface $input): string
    {
        $name = $input->getArgument('name');

        // Add "Page" to the name if it doesn't include it
        if (substr_compare(strtolower($name), 'page', -strlen('page'))) {
            $name .= 'Page';
        }

        return $name;
    }
}