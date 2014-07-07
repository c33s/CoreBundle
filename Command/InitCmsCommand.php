<?php

namespace c33s\CoreBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

use Identicon\Identicon;


use c33s\CoreBundle\Tools\Tools;

use c33s\CoreBundle\Command\BaseInitCmd as BaseInitCommand;

class InitCmsCommand extends BaseInitCommand
{
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
        $this->io->write('<info>c33s:init-cms</info>');
        
        $this->asseticBundles = $input->getOption('bundles');
        $name = $input->getArgument('name');
        $this->initNameHelper($name);
        
        if (!$input->getOption('no-bundles'))
        {
            $bundles = $input->getOption('bundles');
            
            $this->generatePredifinedBundles($bundles);
        }
        
        $this->initTemplatesAndResources();
        $this->addImporterToConfig();
        $this->generateLogo($name);
        $this->generateFavicon($name);
        
        $this->copyFiles();
        if (!$input->getOption('no-executes'))
        {
            $this->executeCommand("php app/console propel:build --insert-sql");
            $this->executeCommand("c dump-autoload");
            $this->executeCommand("php app/console assets:install");
            $this->executeCommand("php app/console c33s:rebuild", 120);
        }
    }
    
    protected function generateLogo($name)
    {
        $logoDirectory = $this->getContainer()->get('kernel')->getRootDir().'/../web/media/images';
        $this->fs->mkdir($logoDirectory);
        $identicon = new Identicon();
        $imageData = $identicon->getImageData($name);
        $this->fs->dumpFile($logoDirectory.'/logo.png', $imageData);
    }
    
    protected function generateFavicon($name)
    {
        $logoDirectory = $this->getContainer()->get('kernel')->getRootDir().'/../web';
        $this->fs->mkdir($logoDirectory);
        $identicon = new Identicon();
        $imageData = $identicon->getImageData($name);
        $this->fs->dumpFile($logoDirectory.'/favicon.ico', $imageData);
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
    
    protected function generateBundle($bundle)
    {
        $command = "php app/console generate:bundle --namespace={$this->name->camelcased()}/${bundle} --dir=src --bundle-name={$this->name->camelcased()}${bundle} --format=yml  --no-interaction";
        $this->executeCommand($command);
    }
    
    protected function renderFileFromTemplate($file, $targetDirectory = null, $parameters = array())
    {
        $parameters['name'] = $this->name;
        $parameters['asseticBundles'] = $this->asseticBundles;
        
        parent::renderFileFromTemplate($file,$targetDirectory,$parameters);
    }
}
