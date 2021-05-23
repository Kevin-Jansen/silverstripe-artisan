<?php


namespace KevinJansen\SilverstripeArtisan\Console;


use KevinJansen\SilverstripeArtisan\Console\Commands\Make\MakePageCommand;
use KevinJansen\SilverstripeArtisan\Console\Commands\Make\MakeThemeCommand;
use KevinJansen\SilverstripeArtisan\Console\Commands\NewProjectCommand;
use KevinJansen\SilverstripeArtisan\Console\Commands\Tasks\DevBuildTask;
use KevinJansen\SilverstripeArtisan\Console\Framework\CLI;
use Symfony\Component\Console\Application;
use SilverStripe\Control\HTTPApplication;
use SilverStripe\Control\HTTPRequestBuilder;
use SilverStripe\Core\CoreKernel;

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

        $this->getSilverstripeInstance();
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
        $this->getApplication()->addCommands([new MakePageCommand, new MakeThemeCommand]);

        // All Task commands
        $this->getApplication()->addCommands([new DevBuildTask]);

        return $this;
    }

    protected function getSilverstripeInstance() {
        if (file_exists(getcwd() . '/vendor/silverstripe/framework/src/Core/CoreKernel.php')) {
            define("SILVERSTRIPE_ROOT", getcwd());

            $this->defineSrc('/app/src', 'SILVERSTRIPE_APP_SRC');
            $this->defineSrc('/themes', 'SILVERSTRIPE_THEME_SRC');

            // Lets require the Silverstripe autoloader so we have access to it's classes
            require_once getcwd() . '/vendor/autoload.php';

            // Boot up the Silverstripe application
            $_SERVER['REQUEST_URI'] = '/';
            $_SERVER['REQUEST_METHOD'] = 'GET';
            $_SERVER['SERVER_PROTOCOL'] = 'http';

            $request = HTTPRequestBuilder::createFromEnvironment();
            $kernel = new CoreKernel(BASE_PATH);
            $app = new HTTPApplication($kernel);
            $app->handle($request);
        }
    }

    private function defineSrc($path, $name) {
        if (is_dir(getcwd() . $path)) {
            $src = getcwd() . $path;

            // If the OS is windows, swap forward slashes to back slashes
            if (PHP_OS_FAMILY == 'Windows') {
                $src = str_replace("/", "\\", $src);
            }
            define($name, $src);
        }
    }
}