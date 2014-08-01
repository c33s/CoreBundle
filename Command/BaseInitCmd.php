<?php

namespace C33s\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;

use C33s\CoreBundle\Command\ConsoleIO;
use C33s\CoreBundle\Helper\NameHelper;
use C33s\CoreBundle\Util\AkelosInflector as Inflector;


class BaseInitCmd extends ContainerAwareCommand
{
    protected $io;
    protected $name;
    protected $fs;
    protected $asseticBundles = array('Webpage', 'Admin', 'AdminGen');
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new ConsoleIO($input, $output, $this->getHelperSet());
        $this->fs = new Filesystem();
        
        if ($input->hasArgument('name'))
        {
            $this->initNameHelper($input->getArgument('name'));
        }
    }
    
    protected function initNameHelper($name)
    {
        if ($this->isFramework())
        {
            $inflector = $this->getContainer()->get('c33s_core.inflector');
        }
        else
        {
            $inflector = new Inflector;
        }
        $this->name = new NameHelper($name, $inflector);
    }
    
    protected function isFramework()
    {
        $app = $this->getApplication();
        if (method_exists($app,'getKernel'))
        {
            return true;
        }
        return false;
    }
    
    protected function executeCommand($command,$timeout=60)
    {
        $this->io->write(sprintf('Running <comment>%s</comment>', $command));
        $process = new Process($command);
        $process->setTimeout($timeout);
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
    
    protected function getCommandTemplateDirectory()
    {
        //$commandTemplateDirectory = basename(get_class($this));
        $reflect = new \ReflectionClass($this);
        return $reflect->getShortName();
    }
    
    protected function initTemplatesAndResources()
    {
        $this->io->write('initing Templates and Resources');
        
        $path = $this->getContainer()->get('kernel')->locateResource('@C33sCoreBundle/Resources/views/Command/'.$this->getCommandTemplateDirectory().'/');
        $bundleNames = $this->getTemplateDirectories($path);
        
        foreach ($bundleNames as $bundleName)
        {
            $path = $this->getContainer()->get('kernel')->locateResource('@C33sCoreBundle/Resources/views/Command/'.$this->getCommandTemplateDirectory().'/'.$bundleName);
            $this->renderFilesFromTemplates($path,$bundleName);
        }
    }
    
    protected function renderFilesFromTemplates($path,$bundleName)
    {
        $this->io->write("rendering files for $bundleName");
        $this->io->write("rendering files in path $path", OutputInterface::VERBOSITY_DEBUG);
        $finder = new Finder();
        $finder->files()->in($path);
        foreach ($finder as $file)
        {
            $this->io->write("copying file '$file'", OutputInterface::VERBOSITY_DEBUG);
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

            $this->renderFileFromTemplate($currentFile,$targetDirectory,array('bundlename' => $bundlename));
        }
    }
    
    
    protected function getTemplateDirectories($path)
    {
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
        
        return $bundleNames;
    }
    
    protected function renderFileFromTemplate($file, $targetDirectory = null, $parameters = array())
    {
        $parameters['name'] = $this->name;
                
        $fileParts = pathinfo($file);
        
        $content = $this->getContainer()->get('templating')->render("C33sCoreBundle:Command/".$this->getCommandTemplateDirectory()."/${fileParts['dirname']}:${fileParts['basename']}", $parameters);
        
        if ($targetDirectory)
        {
            $targetFile = $this->getContainer()->get('kernel')->getRootDir() . '/../'.$targetDirectory.DIRECTORY_SEPARATOR.$fileParts['filename'];
        }
        else
        {
            $targetFile = $this->getContainer()->get('kernel')->getRootDir() . '/../'.$fileParts['dirname'].'/'.$fileParts['filename'];
        }
            
        $this->io->write($targetFile,OutputInterface::VERBOSITY_VERBOSE);
        $this->fs->dumpFile($targetFile, $content);
    }
}