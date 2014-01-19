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

//use Symfony\Component\Filesystem\Filesystem;
//use Symfony\Component\Filesystem\Exception\IOException;

use c33s\CoreBundle\Util\BundleHelper;

class InitConfigCommand extends ContainerAwareCommand
{
    protected $input;
    protected $output;
    
    protected function configure()
    {
        $this
            ->setName('c33s:init-config')
            ->setDescription('the task will init the projects config with the importing system')
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
	$output->writeln('<info>c33s:init-config</info>');
        $this->input = $input;
        $this->output = $output;
	//$this->copyConfigs();
	$this->addBundles();
        //var_dump();
    }
    
    protected function addImporterToConfig()
    {
        $configFile = $this->getConfigYmlPath();
        $configToAdd = "    - { resource: @c33sCoreBundle/Resources/config/config/_importer.yml }\n";
        $stringAfterToInsert = "- { resource: @c33sCoreBundle/Resources/config/config.yml }\n";
        
        Tools::addLineToFile($configFile,$configToAdd,$stringAfterToInsert);
        
        $this->output->writeln('added CoreBundle config.yml to imports');
    }
    
    protected function addBundles()
    {
        $bundles = $this->getContainer()->parameters['c33s_core.config.bundles'];
        foreach ($bundles as $bundle)
        {
            echo $bundle."\n";
        }
        $this->output->writeln('added Bundles');
    }


//    protected function copyConfigs()
//    {
//	$kernel = $this->getContainer()->get('kernel');
//	$sourcePath = $kernel->locateResource('@c33sCoreBundle/Resources/templates/config');
//	$targetPath = $this->getContainer()->getParameter('kernel.root_dir').'/config';
//
//	$fs = new Filesystem();
//	$overwrite = $input->getOption('force');
//	$options = array('override' => $overwrite);
//	$fs->mirror($sourcePath, $targetPath, null, $options);	
//    }
}