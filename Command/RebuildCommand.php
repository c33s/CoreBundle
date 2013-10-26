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
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;

class RebuildCommand extends ContainerAwareCommand
{
    protected $users = array();
    protected $commandSets = array   
    (

	array('description' => 'cache:clear', 'command' => 'cache:clear'),
	array('description' => 'propel:build', 'command' => 'php app/console propel:build --insert-sql'),
	array('description' => 'propel:fixtures:load', 'command' => 'php app/console propel:fixtures:load'),
	//array('description' => 'propel:graphviz:generate', 'command' => 'php ./app/console propel:graphviz:generate'),
	//array('description' => '', 'command' => 'dot -Tpdf ./app/propel/graph/default.schema.dot -o ./schema.pdf'),
	array('description' => 'assets:install', 'command' => 'assets:install'),

	
    );
    protected function configure()
    {
        $this
            ->setName('c33s:rebuild')
            ->setDescription('replaces the rebuild.bat/rebuild.sh. the command builds the model, loads the fixtures and creates the fos users based upon the user.yml')
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
		$fs = new Filesystem();
		$overwrite = $input->getOption('force');
		$options = array('override' => $overwrite);
		$fs->mkdir('app/data');
	
		$yaml = new Parser();
		//$path = $this->getContainer()->getParameter('kernel.root_dir').'/../users.yml';
		$path = $this->getContainer()->getParameter('kernel.root_dir').'/config/users.yml';
		//var_dump(realpath($path),file_get_contents($path));
	
		try
		{
	    	$this->users =  $yaml->parse(file_get_contents($path));
		}
		catch (ParseException $e)
		{
	    	printf("Unable to parse the YAML string: %s", $e->getMessage());
		}
	
		//$yaml->parse(file_get_contents($path));
		//$output->writeln('starting dumping config files');
		//$output->writeln('<info>c33s:rebuild</info>');
	
		//var_dump($this->users);
		//	$this->dumpConfigFiles($output);
		//
		///$commandUserCreate = $this->getApplication()->find('fos:user:create');
		//$commandUserPromote = $this->getApplication()->find('fos:user:promote');
		foreach ($this->users['users'] as $key => $user)
		{
			$this->commandSets[] = array('description' => 'fos:user:create', 'command' => "php app/console fos:user:create ${user['name']} ${user['email']} ${user['password']}");
			foreach ($user['roles'] as $role)
			{
				$this->commandSets[] = array('description' => 'fos:user:promote', 'command' => "php app/console fos:user:promote ${user['name']} ${role}");
			}
		}
	    
	    
		foreach ($this->commandSets as $commandSet)
		{
			$output->writeln(sprintf('Running <comment>%s</comment>', $commandSet['description']));
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
	    //$returnCode = $commandUserCreate->run(new ArrayInput(array('command' => 'fos:user:create', 'gaby' )), $output);
		//	    $output->writeln(sprintf('Running <comment>%s</comment> check.', $commandSet['description']));
		//	    $process = new Process($commandSet['command']);
		//	    $process->run(function ($type, $buffer)
		//	    {
		//		if (Process::ERR === $type)
		//		{
		//		    echo $buffer;
		//		}
		//		else
		//		{
		//		    echo $buffer;
		//		}
		//	    });
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


    //}
    
//    protected function dumpConfigFiles(OutputInterface $output)
//    {
//	$templateing = $this->getContainer()->get('templating');
//	$fs = new Filesystem();
//	if (!$fs->exists('.php_cs'))
//	{
//	    $output->writeln('<comment>starting dumping config files</comment>');
//	    $body = $templateing->render
//	    (
//		'c33sCoreBundle:Command:code_standard_fixer.php.twig',
//		array()
//	    );
//	    try
//	    {
//		$fs->dumpFile('.php_cs', $body);
//	    }
//	    catch (IOException $e)
//	    {
//		$output->writeln('<error>An error occurred while dumping the config files</error>');
//	    }
//
//	    if (!$fs->exists('.php_cs'))
//	    {
//		$output->writeln('<error>Dumping config file failed, file does not exist after dumping</error>');
//	    }
//	    else
//	    {
//		$output->writeln('<comment>done dumping config files</comment>');
//	    }
//	}
//    }
}