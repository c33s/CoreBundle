<?php

namespace C33s\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use C33s\CoreBundle\DataHandler\DataFile;
use Symfony\Component\Finder\Finder;

class HashCopyCommand extends ContainerAwareCommand
{
    // c33s:file service 1 c:\test.txt
    // c33s:file service -> liste

    protected $files = array();

    protected function configure()
    {
         $this
            ->setName('c33s:hashcopy')
            ->setDescription('copies a file to its hash location')
            ->addArgument('file', InputArgument::REQUIRED, 'The Database Table to alter rows in.')
            //->addArgument('target', InputArgument::OPTIONAL, 'Add data to update or enter.' )
            ->addOption('levels', null, InputOption::VALUE_OPTIONAL, 'Int Id or Primary String of Row to select.', 3)
            ->addOption('basedir', null, InputOption::VALUE_OPTIONAL, 'Add a file via path to the Row.', 'web/upload')
            ->addOption('subdir', null, InputOption::VALUE_OPTIONAL, 'Add a file via path to the Row.', '')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command can query the database and allows you
to add or update Fields.

<info>php %command.full_name% service</info>
As no id is given a Table of all Columns is shown.

<info>php %command.full_name% service --id=1</info>
With a id given, only the Service with the given id is shown.

<info>php %command.full_name% service --id=Berat</info>
Providing a non-numeric id searches for the service by its Primary String or even
Parts of it "Berat" finds the Service "Beratung".
Warning the Primary String may not be unique.


EOT
            )
        ;
    }

    /**
     *
     * @return DataFile
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("\nHash Copy\n");

        $file    = $input->getArgument('file');
        $levels  = $input->getOption('levels');
        $basedir = $input->getOption('basedir');
        $subdir  = $input->getOption('subdir');

        $rootdir = $this->getContainer()->get('kernel')->getRootDir();
        $dir     = $rootdir.'/../'.$basedir;

        //return collection
        return $this->copy($file, $dir, $levels, $subdir);
    }

    protected function copy($file, $dir, $levels, $subdir)
    {

        if (is_dir($file))
        {
            $directory = $file;
            $finder    = new Finder();
            $finder
                ->files()
                ->in($directory)
                ->depth('== 0')
            ;
            $dataFileCollection = array();

            foreach ($finder as $file)
            {
                  $dataFile = new DataFile($file, $dir, $levels, $subdir, false);
                  $dataFile->copy();
                  $dataFileCollection[] = $dataFile;
            }

            return $dataFileCollection;
        }
        else
        {
            $dataFile = new DataFile($file, $dir, $levels, $subdir, false);
            $dataFile->copy();

            return $dataFile;
        }
    }
}
