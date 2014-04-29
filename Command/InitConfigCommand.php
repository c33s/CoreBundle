<?php

namespace c33s\CoreBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Filesystem\Filesystem;

use c33s\CoreBundle\Tools\Tools;
use c33s\CoreBundle\Command\BaseInitCmd as BaseInitCommand;

class InitConfigCommand extends BaseInitCommand
{
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
        parent::execute($input, $output);
	$this->io->write('<info>c33s:init-config</info>');
	$this->rebuildBundles();
	$this->createPropelDataDirectory();
    }
    
    protected function addImporterToConfig()
    {
        $configFile = $this->getConfigYmlPath();
        $configToAdd = "    - { resource: @c33sCoreBundle/Resources/config/config/_importer.yml }\n";
        $stringAfterToInsert = "- { resource: security.yml }";
        
        Tools::addLineToFile($configFile,$configToAdd,$stringAfterToInsert);
        
        $this->io->write('added CoreBundle config.yml to imports');
    }
    
    protected function rebuildBundles()
    {
        $bundles = $this->getContainer()->parameters['c33s_core.config.bundles'];
	$bundles = array_reverse($bundles);
	$appKernel = $this->getContainer()->get('kernel')->getRootDir().'/AppKernel.php';
	
	
	$this->removeBundles($appKernel);
	$this->cleanBaseImporter();
	$this->rebuildBaseImporter($bundles);
	$this->addBundles($appKernel,$bundles);
	
        $this->io->write('added Bundles');
    }
    
    protected function createPropelDataDirectory()
    {
	$propelDataDir = $this->getContainer()->get('kernel')->getRootDir().'/config/corebundle';
	
	$fs = new Filesystem();
	$fs->mkdir($propelDataDir);
    }


    protected function rebuildBaseImporter($bundles)
    {
	$coreBundleConfigDir = $this->getContainer()->get('kernel')->getRootDir().'/config/corebundle';
	
	$importerLines = array();
	$importerLines[] = 'imports:';
        foreach ($bundles as $bundle => $properties)
        {
	    $path = $this->getBundleConfigPath($bundle);
	    
	    if ($path !== false)
	    {
		$importerLines[] = "- { resource: @c33sCoreBundle/Resources/config/config/$bundle.yml }";
	    }
        }
	$fs = new Filesystem();
	$fs->dumpFile($coreBundleConfigDir.'/_base_importer.yml', implode("\n", $importerLines));
	
	$this->addBaseImporterYmlToConfig();
	
	$this->io->write('base importer rebuild');
    }
    
    protected function cleanBaseImporter()
    {
	$coreBundleConfigDir = $this->getContainer()->get('kernel')->getRootDir().'/config/corebundle';
	
	Tools::removeLineFromFile($this->getContainer()->get('kernel')->getRootDir().'/config/config.yml','- { resource: corebundle/_base_importer.yml }');
	
	
	$fs = new Filesystem();
	$fs->remove($coreBundleConfigDir);
	$fs->mkdir($coreBundleConfigDir);
    }
    
    protected function addBaseImporterYmlToConfig()
    {
	$configDir = $this->getContainer()->get('kernel')->getRootDir().'/config';
	Tools::addLineToFile($configDir.'/config.yml',"    - { resource: corebundle/_base_importer.yml }\n","- { resource: @c33sCoreBundle/Resources/config/config.yml }");
    }
    
    protected function getBundleConfigPath($bundle)
    {
	$path = false;
	try
	{
	    $path = $this->getContainer()->get('kernel')->locateResource("@c33sCoreBundle/Resources/config/config/$bundle.yml");
	}
	catch (\InvalidArgumentException $e)
	{
	    return false;
	}
	return $path;
    }
    
    protected function removeBundles($appKernel)
    {
	Tools::cropFileByLine($appKernel,"//# Sub Bundles ###", "//### End Core Bundle ###,", 1, -1, true);
    }
    
    protected function addBundles($appKernel,$bundles)
    {
        foreach ($bundles as $bundle => $properties)
        {
	    if ($properties['class'] !== false)
	    {
		$bundleDefinition = "            new ".$properties['class']."(),\n";
		Tools::addLineToFile($appKernel, $bundleDefinition, "//# Sub Bundles ###");
	    }
        }
    }
}