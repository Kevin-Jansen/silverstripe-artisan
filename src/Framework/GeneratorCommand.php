<?php


namespace KevinJansen\SilverstripeArtisan\Console\Framework;


use Symfony\Component\Console\Command\Command;

class GeneratorCommand extends Command
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
     * Generates a directory
     *
     * @param $path
     * @param int $mode
     * @param false $recursive
     * @return bool
     */
    public function generateDirectory($path, $mode = 0755, $recursive = false): bool
    {
        return mkdir($path, $mode, $recursive);
    }

    /**
     * Validates if a given path is a directory
     *
     * @param $directory
     * @return bool
     */
    public function isDirectory($directory): bool
    {
        return is_dir($directory);
    }
}