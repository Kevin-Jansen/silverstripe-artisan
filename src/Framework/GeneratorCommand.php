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
        if (!defined("SILVERSTRIPE_ROOT")) {
            echo 'No Silverstripe installation found. Exiting!';
            exit(1);
        }

        parent::__construct($this->name);

        $this->setDescription($this->description);
    }

    /**
     * Generates a directory
     *
     * @param $path
     * @param int $mode
     * @param false $recursive
     */
    public function generateDirectory($path)
    {
        return mkdir($path);
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

    public function getStub($path) {
        return file_get_contents($path);
    }

    /**
     * Replaces the placeholder values with real values
     *
     * @param $stub
     * @param $name
     * @return array|string|string[]
     */
    public function replaceClass($stub, $name) {
        return str_replace('{{class}}', $name, $stub);
    }

    public function sanitizePath($path) {
        if (PHP_OS_FAMILY == 'Windows') {
            return str_replace("/", "\\", $path);
        }
    }
}