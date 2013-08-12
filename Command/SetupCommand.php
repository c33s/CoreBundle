<?php

namespace c33s\CoreBundle\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Process\Process;

class SetupCommand extends Command
{
    protected $commandSets = array   
    (
	//array('command' => 'cache:clear', '--env' => 'prod'),
	//array('command' => 'cache:clear', '--env' => 'dev'),
	//array('command' => 'assets:install', 'arguments' => array()),
	array('command' => 'assetic:dump', '--env' => 'prod'),
	//'' => '',
	//'' => '',
    );
    protected $commandSets2 = array   
    (
	array('description' => 'cache clear prod', 'command' => 'php app/console cache:clear --env=prod'),
	array('description' => 'cache clear dev', 'command' => 'php app/console cache:clear --env=dev'),
	array('description' => 'assets install', 'command' => 'php app/console assets:install'),
	array('description' => 'assetic dump', 'command' => 'php app/console assetic:dump --env=prod'),
	//array('description' => '', 'command' => ''),
	
	
    );
    protected function configure()
    {
        $this
            ->setName('c33s:setup')
            ->setDescription('c33s Setup calls multiple symfony commands to get the project setup')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

	$output->writeln('<info>c33s:check</info>');

	foreach ($this->commandSets as $commandSet) 
	{
	    $output->writeln(sprintf('Running <comment>%s</comment> check.', $commandSet['description']));
	    $process = new Process($commandSet['command']);
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


//	$command	 = $this->getApplication()->find('assetic:dump');
//	$arguments = array(
//	     'command' => 'assetic:dump',
//	    '--process-isolation' => true,
//	    '--shell' => true,
//	    '--force' => true,
//	    // 'no-debug'  => true,
//	    // 'env'    => 'prod',
//	 );
//	$input		 = new ArrayInput($arguments);
//	$returnCode	 = $command->run($input, $output);
//	foreach ($this->commandSets as $commandSet)
//	{
//
//	    if ($returnCode == 0)
//	    {
//
//	    }
//	}
//	$command = $this->getApplication()->find('cache:clear');
//
//	    $arguments = array(
//		'command' => 'cache:clear',
//		//'name'    => 'Fabien',
//		'--env'  => 'prod',
//	    );
//
//	    $input = new ArrayInput($arguments);
//	    $returnCode = $command->run($input, $output);


    }
}