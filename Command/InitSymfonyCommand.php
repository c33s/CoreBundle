<?php

namespace C33s\CoreBundle\Command;


use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

use Twig_Loader_Filesystem;
use Twig_Environment;

use Symfony\Component\Filesystem\Filesystem;

use Symfony\Component\Finder\Finder;
use C33s\CoreBundle\Tools\Tools;

use C33s\CoreBundle\Command\BaseInitCmd as BaseInitCommand;

class InitSymfonyCommand extends BaseInitCommand
{
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
            ->addArgument('name', InputArgument::OPTIONAL, 'the Name of the Customer (used as Namespace Part)', 'Acme' )
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
        $output->writeln('<info>initializing symfony</info>');
        $this->io->write('<info>c33s:init-cms</info>');
        
        $this->copyData($input->getOption('force'));
        $this->removeAcmeBundle();
        $this->addCoreBundle();
        $this->addConfigYml();
        $this->cleanConfig();
        
        //$this->postInstall($output);
    }
    
    protected function postInstall(OutputInterface $output)
    {
        //how to run 'Incenteev\ParameterHandler\ScriptHandler::buildParameters' here?
        //'Sensio\Bundle\DistributionBundle\Composer\ScriptHandler::buildBootstrap',
        //
        //new Incenteev\ParameterHandler\Processor();
        //use Composer\IO\IOInterface;
        //SensioScriptHandler::doBuildBootstrap($this->getAppDirectory());
        //$this->getAppDirectory()
        //need a composer instance

//        $output->writeln(sprintf('Running <comment>%s</comment>', "$composer $composerScript"));
//        $process = new Process("composer.phar run-script post-update-cmd");
//
//        $process->run(function ($type, $buffer)
//        {
//        if (Process::ERR === $type)
//        {
//                echo $buffer;
//        }
//        else
//        {
//                echo $buffer;
//        }
//        });
    }
    
    protected function addCoreBundle()
    {
        $checkString = "new C33s\CoreBundle\C33sCoreBundle(),";
        $bundleDefinitionToAdd = "\n            //### Core Bundle ###\n            new C33s\CoreBundle\C33sCoreBundle(),\n            //# Sub Bundles ###\n            //### End Core Bundle ###,\n            //new c33s\DummyBundle\c33sDummyBundle(),\n";
        $stringAfterToInsert = 'new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),';
        $appKernelFile = $this->getAppKernelPath();
        Tools::addLineToFile($appKernelFile,$bundleDefinitionToAdd,$stringAfterToInsert,$checkString);
        
        $this->io->write('added CoreBundle to AppKernel');
    }
    
    protected function addConfigYml()
    {
        $configFile = $this->getConfigYmlPath();
        $configToAdd = "    - { resource: @C33sCoreBundle/Resources/config/config.yml }\n";
        $stringAfterToInsert = "- { resource: security.yml }";
        
        Tools::addLineToFile($configFile,$configToAdd,$stringAfterToInsert);
        
        $this->io->write('added CoreBundle config.yml to imports');
    }
    
    protected function cleanConfig()
    {
        $configFile = $this->getConfigYmlPath();
        
        Tools::cropFileByLine($configFile, false, "# Assetic Configuration");
        $this->io->write('config.yml cleaned');
    }
    
    protected function removeAcmeBundle()
    {
        $bundleDefinitionToRemove = '$bundles[] = new Acme\DemoBundle\AcmeDemoBundle();';
        $appKernelFile = $this->getProjectRootDirectory().'/app/AppKernel.php';
        Tools::removeLineFromFile($appKernelFile,$bundleDefinitionToRemove);
        
        $configFile = $this->getRoutingDevYmlPath();
        try
        {
            Tools::cropFileByLine($configFile, false, "# AcmeDemoBundle routes (to be removed)");
        }
        catch (\Exception $e)
        {
            // gone with sf 2.5
        }
        
        $this->io->write('removed AcmeBundle');
    }
    
    protected function copyData($overwrite = false)
    {
        $path = $this->getCoreBundleTemplatesDirectory();
        $finder = new Finder();
        $finder
            ->files()
            ->in($path)
            ->ignoreDotFiles(false)
            ->ignoreVCS(false)
        ;
        
        $parameters = array();
        $parameters['secret'] = $this->generateSecret();
        $parameters['name'] = $this->name;
        foreach ($finder as $file)
        {
            $this->renderTemplateFile($file,$parameters);
        }
        
        $this->copyFramework($overwrite);
    }
    
    protected function renderTemplateFile($file,$parameters = array())
    {
        $fs = new Filesystem();
        $loader = new Twig_Loader_Filesystem($file->getPath());
        $twig = new Twig_Environment($loader);
        $template = $twig->loadTemplate($file->getFilename());
        $content = $template->render($parameters);
        $fileParts = pathinfo($file);
        $targetFile = $this->getRootDirectory().'/'.$file->getRelativePath().'/'.$fileParts['filename'];
        $fs->dumpFile($targetFile, $content);
        $this->io->write($targetFile);
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
        $this->io->write('copied Framework Files');
    }
    
    protected function getProjectRootDirectory()
    {
        return $this->getVendorDirectory().'/..';
    }
    
    protected function getRootDirectory()
    {
        //return __DIR__.'/../../../../..';
        return getcwd();
    }
    
    protected function getVendorDirectory()
    {
        //return __DIR__.'/../../../../..';
        return getcwd().'/vendor';
    }
    
    protected function getSymfonyDirectory()
    {
        $path = $this->getVendorDirectory().'/symfony/framework-standard-edition';
        if (!realpath($path))
        {
            throw new \Exception('Symfony Framwork Standard Edition not found');
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
    
    protected function getRoutingDevYmlPath()
    {
        return $this->getAppDirectory().'/config/routing_dev.yml';
    }
    
    protected function getCoreBundleTemplatesDirectory()
    {
        $path = $this->getCoreBundleDirectory().'/Resources/views/Command/InitSymfonyCommand';
        //var_dump($path);
        if (!realpath($path))
        {
            throw new \Exception('C33sCoreBundle Templates not found');
        }
        return $path;
    }
    
    protected function getCoreBundleDirectory()
    {
        $path = $this->getVendorDirectory().'/c33s/core-bundle';
        if (!realpath($path))
        {
            throw new \Exception('C33sCoreBundle not found');
        }
        return $path;
    }
    
    protected function generateSecret()
    {
        return Tools::generateRandomPassword();
    }
}
