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
//use c33s\CoreBundle\Util\AkelosInflector as Inflector;

//use Composer\IO\ConsoleIO;

use c33s\CoreBundle\Command\ConsoleIO;
use c33s\CoreBundle\Helper\NameHelper;

class InitCmsCommand extends ContainerAwareCommand
{
    protected $io;
    protected $fs;
    protected $bundles;
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
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
	$this->io = new ConsoleIO($input, $output, $this->getHelperSet());
	$this->io->write('<info>c33s:init-cms</info>');
	$this->fs = new Filesystem();
	
	$this->bundles = $input->getOption('bundles');
	$this->initNameHelper($input->getArgument('name'));
	
	if (!$input->getOption('no-bundles'))
	{
	    $this->generatePredifinedBundles();
	}
	$this->generateRouting();
    }
    
    protected function initNameHelper($name)
    {
	$inflector = $this->getContainer()->get('c33s_core.inflector');
	$this->name = new NameHelper($name, $inflector);
    }
    
    protected function generateRouting()
    {
	$this->generateFileFromTemplate('app/config/routing.yml');
	$this->generateFileFromTemplate('app/config/routing_app.yml');
	$this->generateFileFromTemplate('app/config/security.yml');
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
    
    protected function generateFileFromTemplate($file, $parameters = array())
    {
	$fileParts = pathinfo($file);
	
	$parameters['name'] = $this->name;
	
	$content = $this->getContainer()->get('templating')->render("c33sCoreBundle:Command/InitCmsCommand/${fileParts['dirname']}:${fileParts['basename']}.twig", $parameters);
	$targetFile = $this->getContainer()->get('kernel')->getRootDir() . '/../'.$file;
	
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