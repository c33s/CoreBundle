<?php

namespace C33s\CoreBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;


use Symfony\Component\Finder\Finder;

use C33s\CoreBundle\Command\BaseInitCmd as BaseInitCommand;

class BuildAdminGenCommand extends BaseInitCommand
{
    protected function configure()
    {
        $this
            ->setName('admin:c33s:build')
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
               'admin-models',
               null,
               InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
               'supply an array of model names for which admin should be generated',
                array('Storage', 'News')
            )
            ->addOption(
               'append-routing',
               null,
               InputOption::VALUE_OPTIONAL,
               'should the routing be appended to the existing routing data or newly written',
                true
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        $this->io->write('<info>admin:c33s:build</info>');
        $this->adminModels = $input->getOption('admin-models');

        $this->backupMainRoutingFile();
        if (!$input->getOption('no-bundles'))
        {
            $this->generateAdmins($this->adminModels);
        }
        $this->createAdminGenRouting($input->getOption('append-routing'));
        $this->fixAdminGeneratorYmls();
        $this->restoreMainRoutingFile();
    }

    protected function createAdminGenRouting()
    {
        $this->io->write('creating admingen routing');
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

        $filePath = $routingPath . '/admingenerator.yml';

        $oldContent = file_exists($filePath) ? file_get_contents($filePath) : '';
        $content = $oldContent . "\n" . $content;
        $this->fs->dumpfile($filePath, $content);
    }

    protected function backupMainRoutingFile()
    {
        $this->io->write("backing up main routing file");
        $file = $this->getContainer()->get('kernel')->getRootDir().'/config/routing.yml';
        $this->fs->rename($file, $file.'.tmp', true);
    }

    protected function restoreMainRoutingFile()
    {
        $this->io->write("restoring up main routing file");
        $file = $this->getContainer()->get('kernel')->getRootDir().'/config/routing.yml';
        $this->fs->rename($file.'.tmp', $file, true);
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
            $this->fs->dumpFile($file->getPathname(), $newContent);
        }
    }
}
