<?php


namespace KevinJansen\SilverstripeArtisan\Console\Commands\Tasks;


use KevinJansen\SilverstripeArtisan\Console\Framework\TaskCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Silverstripe\ORM\DatabaseAdmin;

class DevBuildTask extends TaskCommand
{
    protected $name = "dev:build";
    protected $description = "Run the dev/build command to migrate the database and flush the config";

    /**
     * Configure this command
     */
    protected function configure()
    {
        $this
            ->addOption('flush', 'f', InputOption::VALUE_NONE, 'Flush the cached configuration');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $output->write("<fg=green>Running the dev/build command</>");
        (new DatabaseAdmin)->build();

        return 0;
    }
}