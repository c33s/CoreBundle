<?php

namespace c33s\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Filesystem\Filesystem;

//use c33s\CoreBundle\Tools\Tools;
use Symfony\Component\Process\Process;
use Symfony\Component\Finder\Finder;
//use c33s\CoreBundle\Util\AkelosInflector as Inflector;

//use Composer\IO\ConsoleIO;

use c33s\CoreBundle\Command\ConsoleIO;
use c33s\CoreBundle\Helper\NameHelper;

class InitCmsCommand extends ContainerAwareCommand
{
    protected $io;
    protected $fs;
    protected $finder;
    protected $bundles;
    protected $asseticBundles;
    protected $name;
    //protected $input;
    //protected $output;
    
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
	$this->io = new ConsoleIO($input, $output, $this->getHelperSet());
	$this->io->write('<info>c33s:init-cms</info>');
	$this->fs = new Filesystem();
	
	$this->bundles = $input->getOption('bundles');
	$this->asseticBundles = $input->getOption('bundles');
	$this->initNameHelper($input->getArgument('name'));
	
	if (!$input->getOption('no-bundles'))
	{
	    $this->generatePredifinedBundles();
	}
	
	$this->initTemplatesAndResources();
    }
    
    protected function initTemplatesAndResources()
    {
	$path = $this->getContainer()->get('kernel')->locateResource('@c33sCoreBundle/Resources/views/Command/InitCmsCommand/');
	$directoryFinder = new Finder();
	$directoryFinder
	    ->directories()
	    ->in($path)
	    ->depth('== 0')	
	;
	
	$bundleNames = array();
	foreach ($directoryFinder as $dir)
	{
	    $bundleNames[] =  $dir->getFilename();
	}
	
	
	foreach ($bundleNames as $bundleName)
	{
	    $path = $this->getContainer()->get('kernel')->locateResource('@c33sCoreBundle/Resources/views/Command/InitCmsCommand/'.$bundleName);
	    $finder = new Finder();
	    $finder->files()->in($path);
	    foreach ($finder as $file) 
	    {
		if ($bundleName == 'General')
		{
		    $bundlename = '';
		    $targetDirectory = $file->getRelativePath();
		}
		else
		{
		    $bundlename = $bundleName;
		    $targetDirectory = "src/{$this->name->camelcased()}/${bundlename}Bundle/".$file->getRelativePath();
		}
		$currentFile = $bundleName.'/'.$file->getRelativePathname();
		
		$this->generateFileFromTemplate($currentFile,$targetDirectory,array('bundlename' => $bundlename));
	    }
	}
    }

    protected function initNameHelper($name)
    {
	$inflector = $this->getContainer()->get('c33s_core.inflector');
	$this->name = new NameHelper($name, $inflector);
    }
        
    protected function generatePredifinedBundles()
    {
	foreach ($this->bundles as $bundle)
	{
	    $this->generateBundle($bundle.'Bundle');
	}
    }
    
    protected function generateBundle($bundle)
    {
	$command = "php app/console generate:bundle --namespace={$this->name->camelcased()}/${bundle} --dir=src --bundle-name={$this->name->camelcased()}${bundle} --format=yml  --no-interaction";
	$this->io->write(sprintf('Running <comment>%s</comment>', $command));
	$process = new Process($command);
	$process->run(function ($type, $buffer)
	{
	    if (Process::ERR === $type)
	    {
		echo $buffer;
	    }
	    else
	    {
		echo $buffer;
	    }
	});
    }
    
    protected function generateFileFromTemplate($file, $targetDirectory = null, $parameters = array())
    {
	$fileParts = pathinfo($file);
	//var_dump($file,$fileParts);
	//exit;
	
	$parameters['name'] = $this->name;
	$parameters['asseticBundles'] = $this->asseticBundles;
	
	$content = $this->getContainer()->get('templating')->render("c33sCoreBundle:Command/InitCmsCommand/${fileParts['dirname']}:${fileParts['basename']}", $parameters);
	
	if ($targetDirectory)
	{
	    $targetFile = $this->getContainer()->get('kernel')->getRootDir() . '/../'.$targetDirectory.DIRECTORY_SEPARATOR.$fileParts['filename'];
	}
	else
	{
	    $targetFile = $this->getContainer()->get('kernel')->getRootDir() . '/../'.$fileParts['dirname'].'/'.$fileParts['filename'];
	}
	    
	
	$this->io->write($targetFile);
	$this->fs->dumpFile($targetFile, $content);
    }
    
    protected function getDefaultHelperSet()
    {
	$helperSet = parent::getDefaultHelperSet();

	$helperSet->set(new DialogHelper());

	return $helperSet;
    }
}