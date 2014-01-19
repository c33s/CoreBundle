<?php

namespace c33s\CoreBundle\Command;

//use Symfony\Component\Console\Command\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
//use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Sensio\Bundle\DistributionBundle\Composer\ScriptHandler;

//use Symfony\Component\Console\Input\ArrayInput;
//use Symfony\Component\Process\Process;

use Symfony\Component\Filesystem\Filesystem;
//use Symfony\Component\Filesystem\Exception\IOException;

use Symfony\Component\Finder\Finder;
use c33s\CoreBundle\Tools\Tools;

class InitSymfonyCommand extends ContainerAwareCommand
{
    protected $input;
    protected $output;
    
    
    protected function configure()
    {
        if ($this->isFramework())
        {
             $this->setName('c33s:init-symfony');
        }
        else
        {
            $this->setName('run');
        }
        $this
            ->setDescription('Inits the project from the sf standard distribution in the vendor dir.')
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
	$output->writeln('<info>initializing symfony</info>');
        $this->input = $input;
        $this->output = $output;
        
	$this->copyData($input->getOption('force'));
	$this->removeAcmeBundle();
	$this->addCoreBundle();
        $this->addConfigYml();
        $this->cleanConfig();
        ScriptHandler::doBuildBootstrap($this->getAppDirectory());
    }
    
    protected function addCoreBundle()
    {
        $checkString = "new c33s\CoreBundle\c33sCoreBundle(),";
	$bundleDefinitionToAdd = "\n            ### Core Bundle ###\n            new c33s\CoreBundle\c33sCoreBundle(),\n            # Sub Bundles ###\n            ### End Core Bundle ###\n\n";
	$stringAfterToInsert = 'new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),';
	$appKernelFile = $this->getAppKernelPath();
	Tools::addLineToFile($appKernelFile,$bundleDefinitionToAdd,$stringAfterToInsert,$checkString);
        
        $this->output->writeln('added CoreBundle to AppKernel');
    }
    
    protected function addConfigYml()
    {
        $configFile = $this->getConfigYmlPath();
        $configToAdd = "    - { resource: @c33sCoreBundle/Resources/config/config.yml }\n";
        $stringAfterToInsert = "- { resource: security.yml }";
        
        Tools::addLineToFile($configFile,$configToAdd,$stringAfterToInsert);
        
        $this->output->writeln('added CoreBundle config.yml to imports');
    }


    protected function cleanConfig()
    {
        $configFile = $this->getConfigYmlPath();
        
        Tools::cropFileByLine($configFile, true, "assetic:");
        $this->output->writeln('config.yml cleaned');
    }
    protected function removeAcmeBundle()
    {
	$bundleDefinitionToRemove = '$bundles[] = new Acme\DemoBundle\AcmeDemoBundle();';
	$appKernelFile = $this->getProjectRootDirectory().'/app/AppKernel.php';
	
	Tools::removeLineFromFile($appKernelFile,$bundleDefinitionToRemove);
        
        $this->output->writeln('removed AcmeBundle');
    }
    
    protected function copyData($overwrite = false)
    {
	
	$fs = new Filesystem();
	$fs->copy($this->getCoreBundleTemplatesDirectory().'/.gitignore', $this->getProjectRootDirectory().'/.gitignore', $overwrite);
	$this->output->writeln('copied .gitignore');
        $fs->copy($this->getCoreBundleTemplatesDirectory().'/parameters.yml.dist', $this->getProjectRootDirectory().'/app/config/parameters.yml.dist', $overwrite);
	$this->output->writeln('copied parameters.yml.dist');
	
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
        $this->output->writeln('copied Framework Files');
    }    
	
    protected function isFramework()
    {
        //$container = $this->getContainer();
        $app = $this->getApplication();
        
        if (method_exists($app,'getKernel'))
        {
            return true;
        }
        return false;
        
        //method_exists($this,'getContainer')
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
    
    protected function getAppKernelPath()
    {
	return $this->getAppDirectory().'/AppKernel.php';
    }
    protected function getAppDirectory()
    {
	return $this->getProjectRootDirectory().'/app';
    }
    protected function getConfigYmlPath()
    {
	return $this->getAppDirectory().'/config/config.yml';
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