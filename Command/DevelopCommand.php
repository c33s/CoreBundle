<?php

namespace c33s\CoreBundle\Command;


use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Filesystem\Filesystem;

use Symfony\Component\Finder\Finder;
use c33s\CoreBundle\Tools\Tools;

use Identicon\Identicon;

use c33s\CoreBundle\Command\BaseInitCmd as BaseInitCommand;

class DevelopCommand extends BaseInitCommand
{
    protected function configure()
    {
        $this->setName('c33s:develop');
        $this
            ->setDescription('Just for developing purposes')
//            ->addArgument('name', InputArgument::OPTIONAL, 'the Name of the Customer (used as Namespace Part)', 'Acme' )
//	    ->addOption(
//               'force',
//               null,
//               InputOption::VALUE_NONE,
//               'If set, the task will overwrite the existing config files'
//            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        $this->io->write('<info>c33s:develop</info>');

        $name = 'Example Name';
        $this->generateLogo($name);
    }
    
    
    
    protected function generateLogo($name)
    {
        $logoDirectory = $this->getContainer()->get('kernel')->getRootDir().'/../web/media/images';
        $this->fs->mkdir($logoDirectory);
        $identicon = new Identicon();
        $imageData = $identicon->getImageData($name);
        $this->fs->dumpFile($logoDirectory.'./logo.png', $imageData);
    }
}