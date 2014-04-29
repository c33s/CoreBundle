<?php

namespace c33s\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Input\InputInterface;
//use Symfony\Component\Console\Input\InputOption;
//use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

use c33s\CoreBundle\Command\ConsoleIO;
use c33s\CoreBundle\Helper\NameHelper;
use c33s\CoreBundle\Util\AkelosInflector as Inflector;

class BaseInitCmd extends ContainerAwareCommand
{
    protected $io;
    protected $name;
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
	$this->io = new ConsoleIO($input, $output, $this->getHelperSet());
	
        if ($input->hasArgument('name'))
        {
            $this->initNameHelper($input->getArgument('name'));
        }
    }
        
    protected function getDefaultHelperSet()
    {
	$helperSet = parent::getDefaultHelperSet();

	$helperSet->set(new DialogHelper());

	return $helperSet;
    }
    
    protected function initNameHelper($name)
    {
        if ($this->isFramework())
        {
            $inflector = $this->getContainer()->get('c33s_core.inflector');
        }
        else
        {
            $inflector = new Inflector;
        }
	$this->name = new NameHelper($name, $inflector);
    }
    
    protected function isFramework()
    {
        $app = $this->getApplication();
        if (method_exists($app,'getKernel'))
        {
            return true;
        }
        return false;
    } 
}