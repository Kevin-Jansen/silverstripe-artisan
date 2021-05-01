<?php


namespace KevinJansen\SilverstripeArtisan\Console;


use KevinJansen\SilverstripeArtisan\Console\Commands\NewProjectCommand;
use Symfony\Component\Console\Application;

class Bootstrap
{
    protected $application;

    /**
     * Boot up the console application
     */
    public function __construct()
    {
        $this->application = new Application;

        $this->bootstrapApplication();
    }

    /**
     * Bootstraps the application
     *
     *  @return $this
     */
    protected function bootstrapApplication()
    {
        $this->application->setName('Silverstripe CLI');
        $this->application->setVersion('0.1');

        $this->initializeCommands();

        return $this;
    }

    /**
     * Initializes the CLI commands
     *
     * @return $this
     */
    protected function initializeCommands()
    {
        $this->application->add(new NewProjectCommand);

        return $this;
    }

    /**
     * Get the application
     *
     * @return Application
     */
    public function getApplication() {
        return $this->application;
    }
}