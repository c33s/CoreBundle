<?php

namespace c33s\CoreBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;


use Symfony\Component\Finder\Finder;
use c33s\CoreBundle\Tools\Tools;

use c33s\CoreBundle\Command\BaseInitCmd as BaseInitCommand;

class InitCmsCommand extends BaseInitCommand
{

    //protected $bundles;
    protected $asseticBundles;
    
    protected function configure()
    {
        $this
            ->setName('c33s:init-cms')
            ->setDescription('')
	    ->addArgument('name', InputArgument::REQUIRED, 'the Name of the Customer (used as Namespace Part)' )
	    ->addOption(
               'force',
               null,
               InputOption::VALUE_NONE,
               'If set, the task will overwrite the existing config files'
            )
	    ->addOption(
               'no-bundles',
               null,
               InputOption::VALUE_NONE,
               'If set no Bundles are generated.'
            )
	    ->addOption(
               'no-executes',
               null,
               InputOption::VALUE_NONE,
               'If set no Bundles are generated.'
            )
	    ->addOption(
               'bundles',
               null,
               InputOption::VALUE_IS_ARRAY | InputArgument::OPTIONAL,
               'supply an array of bundle names which should be generated',
		array('Webpage', 'Admin', 'AdminGen', 'Model')
		//array('Webpage', 'Admin', 'Model')
            )
	    ->addOption(
               'admin-models',
               null,
               InputOption::VALUE_IS_ARRAY | InputArgument::OPTIONAL,
               'supply an array of model names for which admin should be generated',
		array('Storage','News')
		//array('Webpage', 'Admin', 'Model')
            )
	    ->addOption(
               'assetic-bundles',
               null,
               InputOption::VALUE_IS_ARRAY | InputArgument::OPTIONAL,
               'supply an array of bundle names which should be generated',
		array('Webpage', 'Admin', 'AdminGen')
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
	parent::execute($input, $output);
        //$this->io = new ConsoleIO($input, $output, $this->getHelperSet());
	$this->io->write('<info>c33s:init-cms</info>');
	
        
	$this->asseticBundles = $input->getOption('bundles');
	$this->initNameHelper($input->getArgument('name'));
        $this->adminModels = $input->getOption('admin-models');
	
	if (!$input->getOption('no-bundles'))
	{
            $bundles = $input->getOption('bundles');
            
	    $this->generatePredifinedBundles($bundles);
            $this->generateAdmins($this->adminModels);
	}
	
	$this->initTemplatesAndResources();
        $this->createAdminGenRouting();
        $this->fixAdminGeneratorYmls();
        $this->addImporterToConfig();
//        
//        $this->copyFiles();
//	if (!$input->getOption('no-executes'))
//	{
//            $this->executeCommand("php app/console propel:build --insert-sql");
//            $this->executeCommand("c dump-autoload");
//            $this->executeCommand("php app/console assets:install");
//            $this->executeCommand("php app/console c33s:rebuild",120);
//            $this->executeCommand("php app/console admin:c33s:patch");
//	}        
    }
    
    protected function createAdminGenRouting()
    {
        $this->io->write('createing admingen routing');
        $path = $this->getContainer()->get('kernel')->locateResource("@{$this->name->camelcase()}AdminGenBundle");
        $routingPath = "${path}/Resources/config/routing";
        $this->fs->mkdir($routingPath);
        $content="";
        foreach ($this->adminModels as $model)
        {
            $modelLowercase = strtolower($model);
            $content .= <<<EOT
{$this->name->underscore()}_admin_gen_{$modelLowercase}:
    resource: "@{$this->name->camelcase()}AdminGenBundle/Controller/${model}/"
    type:     admingenerator
    prefix:   /{$modelLowercase}

EOT;
        }
        $this->fs->dumpfile($routingPath.'/admingenerator.yml',$content);
    }

    protected function copyFiles()
    {
        $this->io->write('copying default files');
        $sourcePath = $this->getContainer()->get('kernel')->locateResource('@c33sCoreBundle/Resources/files/copy');
        $targetPath = $this->getContainer()->get('kernel')->getRootDir().'/../';
        $this->fs->mirror($sourcePath, $targetPath, null, array('override' => true, 'delete' => false));
    }
    protected function addImporterToConfig()
    {
        $configFile = $this->getContainer()->get('kernel')->getRootDir().'/config/config.yml';
        $configToAdd = "    - { resource: config/_importer.yml }\n";
        $stringAfterToInsert = "- { resource: corebundle/_base_importer.yml }";
        
        Tools::addLineToFile($configFile,$configToAdd,$stringAfterToInsert);
        $this->io->write('added CoreBundle config.yml to imports');
    }
        
    protected function generatePredifinedBundles($bundles)
    {
        $this->io->write('generating bundles');
	foreach ($bundles as $bundle)
	{
            $this->io->write('generating bundle: '.$bundle);
	    $this->generateBundle($bundle.'Bundle');
	}
    }
    
    protected function generateAdmins($models)
    {
        foreach ($models as $model)
        {
            $this->generateAdmin($model);
        }
    }
    
    protected function generateAdmin($model)
    {
        $command = "php app/console admin:generate-admin --namespace=\"{$this->name->camelcased()}/AdminGenBundle\" --generator=\"propel\" --model-name=\"${model}\" --prefix=\"${model}\" --no-interaction --dir=\"./src\"";
        $this->executeCommand($command);
    }
    
    protected function generateBundle($bundle)
    {
	$command = "php app/console generate:bundle --namespace={$this->name->camelcased()}/${bundle} --dir=src --bundle-name={$this->name->camelcased()}${bundle} --format=yml  --no-interaction";
        $this->executeCommand($command);
    }
    
    protected function fixAdminGeneratorYmls()
    {
        $this->io->write("fixing Admin Generator Yamls");
	$path = $this->getContainer()->get('kernel')->getRootDir().'/../src/'.$this->name.'/AdminGenBundle/Resources/config';
	$finder = new Finder();
	$finder
	    ->files()
            ->name('*generator.yml')
	    ->in($path)
	;
        
        foreach ($finder as $file)
        {
            $content = $file->getContents();
            $from = array('model: '.$this->name.'\AdminGenBundle\Model', );
            $to   = array('model: '.$this->name.'\ModelBundle\Model', );
            $newContent = str_replace($from, $to, $content);
            $this->io->write("fixing ".$file->getPathname(), OutputInterface::VERBOSITY_DEBUG);
            $this->fs->dumpFile($file->getPathname(),$newContent);
            
        }
    }
    
    protected function renderFileFromTemplate($file, $targetDirectory = null, $parameters = array())
    {
	$parameters['name'] = $this->name;
	$parameters['asseticBundles'] = $this->asseticBundles;
        
        parent::renderFileFromTemplate($file,$targetDirectory,$parameters);
    }
}