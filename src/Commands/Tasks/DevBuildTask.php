<?php


namespace KevinJansen\SilverstripeArtisan\Console\Commands\Tasks;


use KevinJansen\SilverstripeArtisan\Console\Framework\CLICommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Silverstripe\ORM\DatabaseAdmin;

class DevBuildTask extends CLICommand
{
    protected $name = "dev:build";
    protected $description = "Run the dev/build command to migrate the database and flush the config";

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $output->write("<fg=green>Running the dev/build command</>");
        (new DatabaseAdmin)->build();

        return 0;
    }
}