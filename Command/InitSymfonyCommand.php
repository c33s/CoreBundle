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
    
    protected function removeAcmeBundle()
    {
	$bundleDefinitionToRemove = '$bundles[] = new Acme\DemoBundle\AcmeDemoBundle();';
	$appKernelFile = $this->getProjectRootDirectory().'/app/AppKernel.php';
	
	Tools::removeLineFromFile($appKernelFile,$bundleDefinitionToRemove);
    }
    
    protected function copyData($overwrite = false)
    {
	var_dump($overwrite);
	$finder = new Finder();
	$fs = new Filesystem();
	
	$finder
	    ->files()
	    ->in($this->getSymfonyDirectory())
	    ->ignoreDotFiles(false)
	    ->ignoreVCS(false)
	    ->exclude('Acme')
	    ->notName('.travis.yml')
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
}