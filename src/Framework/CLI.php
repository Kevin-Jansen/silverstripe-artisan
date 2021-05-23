<?php


namespace KevinJansen\SilverstripeArtisan\Console\Framework;


use Symfony\Component\Console\Application;

class CLI
{
    /**
     * This class maintains the Application instance
     */
    protected $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * Get the application
     *
     * @return Application
     */
    public function getApplication()
    {
        return $this->application;
    }
}