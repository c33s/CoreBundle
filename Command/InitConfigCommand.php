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

use c33s\CoreBundle\Tools\Tools;

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
	$this->rebuildBundles();
        
        /*
         * import config, appkernel
         * 
         * add all bundles to appkernel
         * create the _importer.yml for  
         */ 
        //var_dump();
    }
    
    protected function addImporterToConfig()
    {
        $configFile = $this->getConfigYmlPath();
        $configToAdd = "    - { resource: @c33sCoreBundle/Resources/config/config/_importer.yml }\n";
        $stringAfterToInsert = "- { resource: security.yml }";
        
        Tools::addLineToFile($configFile,$configToAdd,$stringAfterToInsert);
        
        $this->output->writeln('added CoreBundle config.yml to imports');
    }
    
    protected function rebuildBundles()
    {
        $bundles = $this->getContainer()->parameters['c33s_core.config.bundles'];
	$bundles = array_reverse($bundles);
	
        $appKernel = $this->getContainer()->get('kernel')->getRootDir().'/AppKernel.php';
	$this->removeBundles($appKernel);
	$this->buildBaseImporter($bundles);
	$this->addBundles($appKernel,$bundles);
	
        $this->output->writeln('added Bundles');
    }
    
    protected function buildBaseImporter($bundles)
    {
	$kernelDir = $this->getContainer()->get('kernel')->getRootDir();
	$configDir = $kernelDir.'/config';
	$coreBundleConfigDir = $configDir.'/corebundle';
	$appKernel = $kernelDir.'/AppKernel.php';
	
	$fs = new Filesystem();
	$fs->remove($coreBundleConfigDir);
	$fs->mkdir($coreBundleConfigDir);
	
	$importerLines = array();
	$importerLines[] = 'imports:';
    
        foreach ($bundles as $bundle => $properties)
        {
	    //if file exists in core bundle config
	    $importerLines[] = "- { resource: @c33sCoreBundle/Resources/config/config/$bundle.yml }";
//	    if ($properties['class'] !== false)
//	    {
//		$bundleDefinition = "            new ".$properties['class']."(),\n";
//		Tools::addLineToFile($appKernel, $bundleDefinition, "# Sub Bundles ###");
//	    }
        }
	$fs->dumpFile($coreBundleConfigDir.'/_base_importer.yml', implode("\n", $importerLines));
	
	
	Tools::addLineToFile($configDir.'/config.yml',"- { resource: security.yml }","    - { resource: config/_base_import.yml }\n");
    }
    
    protected function removeBundles($appKernel)
    {
	Tools::cropFileByLine($appKernel,"# Sub Bundles ###", "### End Core Bundle ###", 1, -1, true);
    }
    
    protected function addBundles($appKernel,$bundles)
    {
        foreach ($bundles as $bundle => $properties)
        {
	    if ($properties['class'] !== false)
	    {
		$bundleDefinition = "            new ".$properties['class']."(),\n";
		Tools::addLineToFile($appKernel, $bundleDefinition, "# Sub Bundles ###");
	    }
        }
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