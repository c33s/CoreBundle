<?php

namespace c33s\CoreBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
//use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
//use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

//use Symfony\Component\Console\Input\ArrayInput;
//use Symfony\Component\Process\Process;

use Symfony\Component\Filesystem\Filesystem;
//use Symfony\Component\Filesystem\Exception\IOException;

class PatchCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('admin:c33s:patch')
            ->setDescription('renames the default admin generator content block')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
	$this->patchAdmingenerator();
    }
    
    protected function patchAdmingenerator()
    {
        $vendorPath = "vendor/cedriclombardot/admingenerator-generator-bundle/Admingenerator/GeneratorBundle";
        $sourcePath = $this->getContainer()->get('kernel')->locateResource('@c33sCoreBundle/Resources/patches/Admingenerator');
        $targetPath = $this->getContainer()->get('kernel')->getRootDir().'/../'.$vendorPath."/";
        $fs = new Filesystem();
        $fs->mirror(sourcePath, $targetPath, null, array('override' => true, 'delete' => false));
    }
}