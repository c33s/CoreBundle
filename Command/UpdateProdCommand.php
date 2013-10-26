<?php

namespace c33s\CoreBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Process\Process;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

class UpdateProdCommand extends ContainerAwareCommand
{
    protected $commandSets = array   
    (
		array('description' => 'git reset', 'command' => 'git reset --hard'),
		array('description' => 'git pull', 'command' => 'git pull'),
		array('description' => 'composer install', 'command' => 'composer.phar install'),
		array('description' => 'cache clear prod', 'command' => 'php app/console c33s:clean'),
		array('description' => 'chown', 'command' => 'www-data:www-data -R *'),
		//array('description' => '', 'command' => ''),
    );
    protected function configure()
    {
        $this
            ->setName('c33s:updateprod')
            ->setDescription('updates production installation by calling git reset, git pull, composer install,...')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

	$output->writeln('<info>c33s:updateprod</info>');

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
    }
}