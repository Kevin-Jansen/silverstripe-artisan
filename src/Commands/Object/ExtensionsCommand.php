<?php


namespace KevinJansen\SilverstripeArtisan\Console\Commands\Object;

use KevinJansen\SilverstripeArtisan\Console\Framework\CLICommand;
use KevinJansen\SilverstripeArtisan\Console\Framework\Utility\ObjectUtilities;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Injector\Injector;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * List all extensions of a given Object, e.g. "Page"
 *
 * @package silverstripe-console
 * @author  Robbie Averill <robbie@averill.co.nz>
 */
class ExtensionsCommand extends CLICommand
{
    use ObjectUtilities;

    protected $name = "object:extensions";
    protected $description = "List all Extensions of a given Object, e.g. 'Page'";

    protected function configure()
    {
        $this
            ->addArgument('object', InputArgument::REQUIRED, 'The Object to find Extensions for');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $object = $input->getArgument('object');

        $instance = Injector::inst()->create($object);
        // Not all classes implement Extensible
        if (!method_exists($instance, 'get_extensions')) {
            $output->writeln('<error>' . $object . ' doesn\'t allow extensions (implement Extensible)</error>');
            return 0;
        }

        $extensions = $instance->get_extensions();
        if (!$extensions) {
            $output->writeln('<error>There are no Extensions registered for ' . $object . '</error>');
            return 0;
        }
        sort($extensions);

        $output->writeln('<info>Extensions for ' . $object . ':</info>');
        $table = new Table($output);
        $table
            ->setHeaders($this->getHeaders())
            ->setRows($this->getRows($extensions))
            ->render();

        return 0;
    }

    /**
     * Return the header cells for the output table. CMS classes have an extra column.
     *
     * @return string[]
     */
    public function getHeaders()
    {
        return ['Class name', 'Module', 'Added DB fields'];
    }

    /**
     * Return the rows for the output table containing extension statistics.
     *
     * @return string[]
     */
    public function getRows($extensions)
    {
        $tableRows = [];
        foreach ($extensions as $extensionClass) {
            $row = [
                $extensionClass,
                // Add the module name
                $this->getModuleName($extensionClass),
                // Add the number of DB fields that the class adds
                count((array) Config::inst()->get($extensionClass, 'db', Config::UNINHERITED)),
            ];

            $tableRows[] = $row;
        }
        return $tableRows;
    }
}