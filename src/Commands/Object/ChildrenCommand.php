<?php


namespace KevinJansen\SilverstripeArtisan\Console\Commands\Object;


use KevinJansen\SilverstripeArtisan\Console\Framework\CLICommand;
use KevinJansen\SilverstripeArtisan\Console\Framework\Utility\ObjectUtilities;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use SilverStripe\Core\ClassInfo;

class ChildrenCommand extends CLICommand
{
    use ObjectUtilities;

    protected $name = "object:children";
    protected $description = "List all child classes of a given class, e.g. 'Page'";

    /**
     * Configures this command
     */
    protected function configure()
    {
        $this
            ->addArgument('object', InputArgument::REQUIRED, 'The class to find children for');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $object = $input->getArgument('object');
        $classes = (array) ClassInfo::subclassesFor($object);
        // Remove the class itself
        array_shift($classes);
        if (!$classes) {
            $output->writeln('There are no child classes for ' . $object);
            return 0;
        }
        sort($classes);
        $rows = array_map(function ($class) {
            return [$class, $this->getModuleName($class)];
        }, $classes);

        $output->writeln('<info>Child classes for ' . $object . ':</info>');
        $table = new Table($output);
        $table
            ->setHeaders(['Class name', 'Module'])
            ->setRows($rows)
            ->render();

        return 0;
    }
}