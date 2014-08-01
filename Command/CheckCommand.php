<?php

namespace C33s\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class CheckCommand extends ContainerAwareCommand
{
    protected $commandSets = array
    (
        array('description' => 'PhpUnit', 'command' => 'phpunit -c app/'),
        array('description' => 'Php Mess Detector', 'command' => 'phpmd .\src text  codesize,unusedcode,naming,controversial,design --exclude Model'),
        array('description' => 'Code Standard Fixer', 'command' => 'phpfix fix --verbose --dry-run ./'),
        array('description' => 'Php Code Sniffer', 'command' => 'phpcs ./src --ignore=Model/*/Base --standard=PSR1'),
        array('description' => 'Sensio Security Checker', 'command' => 'security-checker security:check'),
        array('description' => 'Copy&Paste Dedector', 'command' => 'copypaste --progress --exclude Model ./src'),
        // --- array('description' => 'Pdepend', 'command' => 'pdepend --jdepend-chart=tmp/chart.png --jdepend-xml=tmp/depend.xml --overview-pyramid=tmp/pyramid.png --summary-xml=tmp/summary.xml ./src'),
        array('description' => 'phploc', 'command' => 'phploc --progress --exclude Model --count-tests ./src'),
    );
    
    protected function configure()
    {
        $this
            ->setName('c33s:check')
            ->setDescription('c33s:check calls multiple Tests to check the project')
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>c33s:check</info>');
        $this->dumpConfigFiles($output);
        
        foreach ($this->commandSets as $commandSet)
        {
            $this->processCommandSet($commandSet, $output);
        }
    }
    
    protected function processCommandSet($commandSet, OutputInterface $output)
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
    
    protected function dumpConfigFiles(OutputInterface $output)
    {
        $templateing = $this->getContainer()->get('templating');
        $fs = new Filesystem();
        if (!$fs->exists('.php_cs'))
        {
            $output->writeln('<comment>starting dumping config files</comment>');
            $body = $templateing->render
            (
                'C33sCoreBundle:Command:code_standard_fixer.php.twig',
                array()
            );
            try
            {
                $fs->dumpFile('.php_cs', $body);
            }
            catch (IOException $e)
            {
                $output->writeln('<error>An error occurred while dumping the config files</error>');
            }

            if (!$fs->exists('.php_cs'))
            {
                $output->writeln('<error>Dumping config file failed, file does not exist after dumping</error>');
            }
            else
            {
                $output->writeln('<comment>done dumping config files</comment>');
            }
        }
    }
}
