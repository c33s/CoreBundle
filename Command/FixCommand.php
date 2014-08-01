<?php

namespace C33s\CoreBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FixCommand extends CheckCommand
{
    protected $commandSets = array
    (
        array('description' => 'Code Standard Fixer', 'command' => 'phpfix fix --verbose ./'),
        //--array('description' => 'Php Code Sniffer', 'command' => 'phpcs ./src --ignore=Model/*/Base --standard=PSR1'),
        //--array('description' => 'Sensio Security Checker', 'command' => 'security-checker security:check'),
        //--array('description' => 'Copy&Paste Dedector', 'command' => 'copypaste --progress ./src'),
        //---array('description' => 'Pdepend', 'command' => 'pdepend --jdepend-chart=tmp/chart.png --jdepend-xml=tmp/depend.xml --overview-pyramid=tmp/pyramid.png --summary-xml=tmp/summary.xml ./src'),
        //--array('description' => 'phploc', 'command' => 'phploc --progress --exclude Model --count-tests ./src'),
    );

    protected function configure()
    {
        $this
            ->setName('c33s:fix')
            ->setDescription('c33s:fix calls the PHP code standards fixer phpfix')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>c33s:fix</info>');
        $this->dumpConfigFiles($output);

        foreach ($this->commandSets as $commandSet)
        {
            $this->processCommandSet($commandSet, $output);
        }
    }
}
