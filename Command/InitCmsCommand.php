<?php

namespace c33s\CoreBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;


use Symfony\Component\Finder\Finder;

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
               'bundles',
               null,
               InputOption::VALUE_IS_ARRAY | InputArgument::OPTIONAL,
               'supply an array of bundle names which should be generated',
		array('Webpage', 'Admin', 'AdminGen', 'Model')
		//array('Webpage', 'Admin', 'Model')
            )
	    ->addOption(
               'models',
               null,
               InputOption::VALUE_IS_ARRAY | InputArgument::OPTIONAL,
               'supply an array of model names for which admin should be generated',
		array('Storage')
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
	
	if (!$input->getOption('no-bundles'))
	{
            $bundles = $input->getOption('bundles');
            $models = $input->getOption('models');
            
	    $this->generatePredifinedBundles($bundles);
            $this->generateAdmins($models);
	}
	
	$this->initTemplatesAndResources();
        $this->fixAdminGeneratorYmls();
        
        $this->executeCommand("php app/console propel:build --insert-sql");
    }
    

        
    protected function generatePredifinedBundles($bundles)
    {
	foreach ($bundles as $bundle)
	{
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