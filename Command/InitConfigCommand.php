<?php

namespace c33s\CoreBundle\Command;


//
//use Symfony\Component\Console\Command\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Process\Process;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

class InitConfigCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('c33s:initconfig')
            ->setDescription('')
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
	//$output->writeln('starting dumping config files');
	$output->writeln('<info>c33s:</info>');
	
	//$templateing = $this->getContainer()->get('templating');
	$kernel = $this->getContainer()->get('kernel');
	$sourcePath = $kernel->locateResource('@c33sCoreBundle/Resources/templates/config');
	$targetPath = $this->getContainer()->getParameter('kernel.root_dir').'/config';
	//var_dump($sourcePath,$targetPath);
	

	$fs = new Filesystem();
	//var_dump($name = $input->getOption('force'));
	$overwrite = $input->getOption('force');
	$options = array('override' => $overwrite);
	$fs->mirror($sourcePath, $targetPath, null, $options);
    }
}