<?php


namespace KevinJansen\SilverstripeArtisan\Console\Framework;


use Symfony\Component\Console\Command\Command;

class CLICommand extends Command
{
    /**
     * The command name
     *
     * @var string
     */
    protected $name;

    /**
     * The command description
     *
     * @var string
     */
    protected $description;

    /**
     * GeneratorCommand constructor.
     */
    public function __construct()
    {
        parent::__construct($this->name);
        $this->setDescription($this->description);
    }

    /**
     * Validates whether the silverstripe root global is set
     */
    public function validateSilverstripe() {
        if (!defined("SILVERSTRIPE_ROOT")) {
            echo 'No Silverstripe installation found. Exiting!';
            exit(1);
        }
    }
}