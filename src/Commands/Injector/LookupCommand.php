<?php


namespace KevinJansen\SilverstripeArtisan\Console\Commands\Injector;


use KevinJansen\SilverstripeArtisan\Console\Framework\CLICommand;
use KevinJansen\SilverstripeArtisan\Console\Framework\Utility\ObjectUtilities;
use Symfony\Component\Console\Input\InputArgument;
use SilverStripe\Core\Injector\Injector;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LookupCommand extends CLICommand
{
    use ObjectUtilities;

    protected $name = "injector:lookup";
    protected $description = "Shows which class is returned from an Injector reference";

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->addArgument('className', InputArgument::REQUIRED, 'The class name to look up');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // First, let's validate that we're working in a SilverStripe project
        $this->validateSilverstripe();

        $className = $input->getArgument('className');
        $resolvedTo = get_class(Injector::inst()->get($className));

        $output->writeln('<comment>' . $className . '</comment> resolves to <info>' . $resolvedTo . '</info>');
        if ($module = $this->getModuleName($resolvedTo)) {
            $output->writeln('<info>Module:</info> <comment>' . $module . '</comment>');
        }

        return 0;
    }
}