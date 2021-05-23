<?php


namespace KevinJansen\SilverstripeArtisan\Console\Commands;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class NewProjectCommand extends Command
{
    /**
     * Configure this command
     */
    protected function configure()
    {
        $this
            ->setName('new')
            ->setDescription('Create a new Silverstripe application')
            ->addArgument('name', InputArgument::REQUIRED, 'Sets the name of the new Silverstripe project')
            ->addOption('git', null, InputOption::VALUE_NONE, 'Creates a new local GIT repository for this project')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Forces the install, even if the directory already exists');
    }

    /**
     * Executes this command
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     * @author This command is forked & altered from Laravel/Installer by Taylor Otwell
     *
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write(PHP_EOL . "<fg=blue>   ___ _ _                _       _           
 / __(_) |_ _____ _ _ __| |_ _ _(_)_ __  ___ 
 \__ \ | \ V / -_) '_(_-<  _| '_| | '_ \/ -_)
 |___/_|_|\_/\___|_| /__/\__|_| |_| .__/\___|
                                  |_|        </>" . PHP_EOL);

        $name = $input->getArgument('name');
        $directory = $name !== '.' ? getcwd() . '/' . $name : '.';

        if (!$input->getOption('force')) {
            $this->verifyApplicationDoesntExist($directory);
        }

        if ($input->getOption('force') && $directory === '.') {
            throw new RuntimeException('Cannot use --force option when using current directory for installation!');
        }

        $commands = [
            "composer create-project silverstripe/installer \"$directory\" --remove-vcs --prefer-dist",
        ];

        if ($directory != '.' && $input->getOption('force')) {
            if (PHP_OS_FAMILY == 'Windows') {
                array_unshift($commands, "rd /s /q \"$directory\"");
            } else {
                array_unshift($commands, "rm -rf \"$directory\"");
            }
        }

        $output->write('Creating new project in <fg=red>' . $directory . '</>' . PHP_EOL);

        if (($process = $this->executeCLIScripts($commands, $input, $output))->isSuccessful()) {
            $this->copyEnv($directory, $input, $output);
            if ($name !== '.') {
                $this->replaceInFile(
                    'SS_DATABASE_NAME="<database>"',
                    'SS_DATABASE_NAME=' . str_replace('-', '_', strtolower($name)),
                    $directory . '/.env'
                );

                $this->replaceInFile(
                    'SS_DATABASE_NAME="<database>"',
                    'SS_DATABASE_NAME=' . str_replace('-', '_', strtolower($name)),
                    $directory . '/.env.example'
                );
            }

            if ($input->getOption('git')) {
                $this->createNewRepository($directory, $input, $output);
            }

            $output->writeln(PHP_EOL . '<comment>Application ready! Build something amazing.</comment>');
        }

        return $process->getExitCode();
    }

    private function copyEnv(string $directory, InputInterface $input, OutputInterface $output)
    {
        chdir($directory);

        $commands = [
            "@php -r \"file_exists('.env') || copy('.env.example', '.env');\""
        ];

        $this->executeCLIScripts($commands, $input, $output);
    }

    private function createNewRepository(string $directory, InputInterface $input, OutputInterface $output)
    {
        $output->write(PHP_EOL . '<comment>Initializing empty repository.</comment>' . PHP_EOL);

        chdir($directory);

        $commands = [
            'git init -q',
            'git add .',
            'git commit -q -m "Set up a fresh Silverstripe application"'
        ];

        $this->executeCLIScripts($commands, $input, $output, ['GIT_TERMINAL_PROMPT' => 0]);
    }

    /**
     * Verify that the application does not already exist.
     *
     * @param string $directory
     * @return void
     */
    protected function verifyApplicationDoesntExist(string $directory)
    {
        if ((is_dir($directory) || is_file($directory)) && $directory != getcwd()) {
            throw new RuntimeException('Application already exists!');
        }
    }

    /**
     * Executes the given CLI commands
     *
     * @param $commands
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param array $env
     * @return Process
     */
    protected function executeCLIScripts($commands, InputInterface $input, OutputInterface $output, array $env = [])
    {
        $process = Process::fromShellCommandline(implode(' && ', $commands), null, $env, null, null);

        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            try {
                $process->setTty(true);
            } catch (RuntimeException $e) {
                $output->writeln('Warning: ' . $e->getMessage());
            }
        }

        $process->run(function ($type, $line) use ($output) {
            $output->write('    ' . $line);
        });

        return $process;
    }

    /**
     * Replace the given string in the given file.
     *
     * @param string $search
     * @param string $replace
     * @param string $file
     * @return void
     */
    protected function replaceInFile(string $search, string $replace, string $file)
    {
        file_put_contents(
            $file,
            str_replace($search, $replace, file_get_contents($file))
        );
    }
}