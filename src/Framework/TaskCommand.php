<?php


namespace KevinJansen\SilverstripeArtisan\Console\Framework;


use Symfony\Component\Console\Command\Command;

class TaskCommand extends Command
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
     * TaskCommand constructor.
     */
    public function __construct()
    {
        if (!defined("SILVERSTRIPE_ROOT")) {
            echo 'No Silverstripe installation found. Exiting!';
            exit(1);
        }

        parent::__construct($this->name);
        $this->setDescription($this->description);
    }
}