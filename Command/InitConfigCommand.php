<?php

namespace c33s\CoreBundle\Command;


//
//use Symfony\Component\Console\Command\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
//use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

//use Symfony\Component\Console\Input\ArrayInput;
//use Symfony\Component\Process\Process;

use Symfony\Component\Filesystem\Filesystem;
//use Symfony\Component\Filesystem\Exception\IOException;

use c33s\CoreBundle\Util\BundleHelper;

class InitConfigCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('c33s:initconfig')
            ->setDescription('the task will init the project by coping the template config files (config/assettic.yml, propel.yml,...) to the fresh project. do not call this command if you have allready set up your project.')
	    ->addOption(
               'force',
               null,
               InputOption::VALUE_NONE,
               'If set, the task will overwrite the existing config files'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
	$output->writeln('<info>c33s:</info>');
	
	//$this->copyConfigs();
	$this->addBundles();
    }
    
    protected function addBundles()
    {
	echo "foo";
	$kernel = $this->getContainer()->get('kernel');
	$bundleHelper = new BundleHelper($kernel);
    }


    protected function copyConfigs()
    {
	$kernel = $this->getContainer()->get('kernel');
	$sourcePath = $kernel->locateResource('@c33sCoreBundle/Resources/templates/config');
	$targetPath = $this->getContainer()->getParameter('kernel.root_dir').'/config';

	$fs = new Filesystem();
	$overwrite = $input->getOption('force');
	$options = array('override' => $overwrite);
	$fs->mirror($sourcePath, $targetPath, null, $options);	
    }
}