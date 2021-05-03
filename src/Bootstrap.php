<?php


namespace KevinJansen\SilverstripeArtisan\Console;


use KevinJansen\SilverstripeArtisan\Console\Commands\Make\MakePageCommand;
use KevinJansen\SilverstripeArtisan\Console\Commands\NewProjectCommand;
use KevinJansen\SilverstripeArtisan\Console\Framework\CLI;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

class Bootstrap extends CLI
{
    /**
     * Boot up the console application
     */
    public function __construct()
    {
        parent::__construct(new Application);

        $this->bootstrapApplication();
    }

    /**
     * Bootstraps the application
     *
     * @return $this
     */
    protected function bootstrapApplication()
    {
        $this->getApplication()->setName('Silverstripe CLI');
        $this->getApplication()->setVersion('0.1');

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
        $this->getApplication()->add(new NewProjectCommand);

        // All Make commands
        $this->getApplication()->addCommands([new MakePageCommand]);

        return $this;
    }
}