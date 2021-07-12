<?php


namespace KevinJansen\SilverstripeArtisan\Console\Commands\Make;


use KevinJansen\SilverstripeArtisan\Console\Framework\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MakeThemeCommand extends GeneratorCommand
{
    protected $name = "make:theme";
    protected $description = "Generate a new Silverstripe theme";

    /**
     * Configure this command
     */
    protected function configure()
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'Sets the name of the new Silverstripe theme')
            ->addOption('force', 'force', InputOption::VALUE_NONE, 'Overwrite if the theme already exists');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // First, let's validate that we're working in a SilverStripe project
        $this->validateSilverstripe();

        // Next, let's get an absolute path where the new theme should be stored
        $name = $input->getArgument('name');
        $path = $this->sanitizePath(SILVERSTRIPE_THEME_SRC . "/$name");

        // Let's check if this directory already exists
        if (!$input->getOption('force')) {
            if ($this->isDirectory($path)) {
                $output->write("<fg=red>Path already exists! Exiting.</>");
                return 1;
            }
        }

        // Next, we'll start generating the directory structure
        $output->write("<fg=yellow>Generating theme: $name at $path \n</>");
        $this->generateDirectories($path);

        // Next, we'll start replacing the Stub file after which we write them to the filesystem
        // We'll start by populating the templates folder
        $this->populateTemplateFolder($path);

        // After which we'll start populating the asset folders, css, js
        $this->populateAssetFolders($path);

        $output->write("<fg=green>Theme generated. Don't forget to update your theme config!</>");
        return 0;
    }

    /**
     * Generate all theme directories
     *
     * @param $path
     */
    private function generateDirectories($path)
    {
        // Let's create the theme folder and fill it with the subdirectories
        $this->generateDirectory("$path");

        foreach (["css", "images", "javascript", "templates", "templates/Includes", "templates/Layout", "webfonts"] as $dir) {
            $this->generateDirectory("$path/$dir");
        }
    }

    /**
     * @param $path
     */
    private function populateTemplateFolder($path)
    {
        file_put_contents($this->sanitizePath("$path/templates/Page.ss"), $this->getStub(__DIR__ . '/stubs/templates.page.stub'));
        file_put_contents($this->sanitizePath("$path/templates/Layout/Page.ss"), $this->getStub(__DIR__ . '/stubs/templates.Layout.page.stub'));
    }

    /**
     * @param $path
     */
    private function populateAssetFolders($path) {
        file_put_contents($this->sanitizePath("$path/javascript/app.js"), "// Create something beautiful");
        file_put_contents($this->sanitizePath("$path/css/main.css"), "/*Create something beautiful*/");
    }
}