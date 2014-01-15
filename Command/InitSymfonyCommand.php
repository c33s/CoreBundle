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

use Symfony\Component\Finder\Finder;
use c33s\CoreBundle\Tools\Tools;

class InitSymfonyCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('c33s:init-symfony')
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
	$this->copyData($input->getOption('force'));
	$this->removeAcmeBundle();
	$this->addCoreBundle();
    }
    
//    protected function removeLineFromFile($file,$stringToRemove)
//    {
//	$lines = file($file);
//	
//	for($i=0;$i<count($lines);$i++)
//	{
//	    if (strstr($lines[$i],$stringToRemove))
//	    {
//		unset($lines[$i]);
//	    }
//	}
//	file_put_contents($file, $lines);
//    }
    
    protected function addCoreBundle()
    {
	$bundleDefinitionToAdd = "\n            new c33s\CoreBundle\c33sCoreBundle(),\n";
	$stringAfterToInsert = 'new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),';
	$appKernelFile = $this->getAppKernelPath();
	Tools::addLineToFile($appKernelFile,$bundleDefinitionToAdd,$stringAfterToInsert);
    }
    
    protected function getAppKernelPath()
    {
	return $this->getProjectRootDirectory().'/app/AppKernel.php';
    }
    protected function removeAcmeBundle()
    {
	$bundleDefinitionToRemove = '$bundles[] = new Acme\DemoBundle\AcmeDemoBundle();';
	$appKernelFile = $this->getProjectRootDirectory().'/app/AppKernel.php';
	
	Tools::removeLineFromFile($appKernelFile,$bundleDefinitionToRemove);
    }
    
    protected function copyData($overwrite = false)
    {
	
	$fs = new Filesystem();
	$fs->copy(getCoreBundleTemplatesDirectory().'/.gitignore', $this->getProjectRootDirectory().'/.gitignore', $overwrite);
	$fs->copy(getCoreBundleTemplatesDirectory().'/parameters.yml.dist', $this->getProjectRootDirectory().'/app/config/parameters.yml.dist', $overwrite);
	
	$this->copyFramework($overwrite);
    }
    
    protected function copyFramework($overwrite = false)
    {
	$finder = new Finder();
	$fs = new Filesystem();
	
	$finder
	    ->files()
	    ->in($this->getSymfonyDirectory())
	    ->ignoreDotFiles(false)
	    ->ignoreVCS(false)
	    ->exclude('Acme')
	    ->notName('.travis.yml')
	    ->notName('parameters.yml.dist')
	    ->notName('.gitignore')
	    ->notName('config.php')
	    ->notName('UPGRADE*.md')
	    ->notName('LICENSE')
	    ->notName('README.md')
	    ->notName('composer.json')
	;
	foreach ($finder as $file) 
	{
	    $fs->copy($file->getRealpath(), $this->getProjectRootDirectory().'/'.$file->getRelativePathname(), $overwrite);
	}	
    }    
	
    
    protected function getProjectRootDirectory()
    {
	return $this->getVendorDirectory().'/..';
    }
    
    protected function getVendorDirectory()
    {
	return __DIR__.'/../../../../..';
    }
    
    protected function getSymfonyDirectory()
    {
	$path = $this->getVendorDirectory().'/symfony/framework-standard-edition';
	if (!realpath($path))
	{
	    throw new Exception('Symfony Framwork Standard Edition not found');
	}
	return $path;
    }
    
    
    protected function getCoreBundleTemplatesDirectory()
    {
	$path = $this->getCoreBundleDirectory().'/Resources/templates';
	if (!realpath($path))
	{
	    throw new Exception('c33sCoreBundle Templates not found');
	}
	return $path;
    }
    protected function getCoreBundleDirectory()
    {
	$path = $this->getVendorDirectory().'/c33s/core-bundle/c33s/CoreBundle';
	if (!realpath($path))
	{
	    throw new Exception('c33sCoreBundle not found');
	}
	return $path;
    }
}