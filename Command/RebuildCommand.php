<?php

namespace C33s\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Process\Process;

use Symfony\Component\Filesystem\Filesystem;

class RebuildCommand extends ContainerAwareCommand
{
    protected $users = array();
    protected $commandSets = array
    (

        array('description' => 'cache:clear', 'command' => 'php app/console cache:clear'),
        array('description' => 'cache:clear', 'command' => 'php app/console cache:clear --env=prod'),
        array('description' => 'propel:build', 'command' => 'php app/console propel:build --insert-sql'),
        array('description' => 'propel:fixtures:load', 'command' => 'php app/console propel:fixtures:load'),
        //-- array('description' => 'propel:graphviz:generate', 'command' => 'php ./app/console propel:graphviz:generate'),
        //-- array('description' => '', 'command' => 'dot -Tpdf ./app/propel/graph/default.schema.dot -o ./schema.pdf'),
        array('description' => 'assets:install', 'command' => 'php app/console assets:install'),
        array('description' => 'cache:warmup', 'command' => 'app/console cache:warmup'),


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
        $fs->mkdir('app/data');

        $this->addCreateFosUsersToCommandSet();
        $this->runCommandSets($input,$output);
    }

    protected function runCommandSets(InputInterface $input, OutputInterface $output)
    {
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
    }

    protected function addCreateFosUsersToCommandSet()
    {
        $users = $this->getContainer()->getParameter('fos_users');
        foreach ($users as $key => $user)
        {
                $this->commandSets[] = array('description' => 'fos:user:create', 'command' => "php app/console fos:user:create ${user['name']} ${user['email']} ${user['password']}");
                foreach ($user['roles'] as $role)
                {
                        $this->commandSets[] = array('description' => 'fos:user:promote', 'command' => "php app/console fos:user:promote ${user['name']} ${role}");
                }
        }
    }
}
