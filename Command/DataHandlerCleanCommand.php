<?php

namespace c33s\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

//
//use c33s\ModelBundle\Model\Service;
//use c33s\ModelBundle\Model\ServiceQuery;
//
//use Symfony\Component\HttpFoundation\File\File;
//use Symfony\Component\Filesystem\Filesystem;
//use Symfony\Component\Filesystem\Exception\IOException;

class DataHandlerCleanCommand extends ContainerAwareCommand
{

    // c33s:file service 1 c:\test.txt
    // c33s:file service -> liste

    protected $files = array();

    protected function configure()
    {
	$this
		->setName('c33s:data:cleaner')
		//->setDescription('Queries and updates the Database including File insertion')
		->addArgument('action', InputArgument::OPTIONAL, 'the command to execute', null)
		//->addArgument('data', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, 'Add data to update or enter.' )
		->addOption('force', true, InputOption::VALUE_NONE, 'required to really delete the files')
	//->addOption('file',array(), InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL, 'Add a file via path to the Row.')
//            ->setHelp(<<<EOT
//The <info>%command.name%</info> command can query the database and allows you
//to add or update Fields.
//
//<info>php %command.full_name% service</info>
//As no id is given a Table of all Columns is shown.
//		    
//<info>php %command.full_name% service --id=1</info>
//With a id given, only the Service with the given id is shown.		    
//		    
//<info>php %command.full_name% service --id=Berat</info>
//Providing a non-numeric id searches for the service by its Primary String or even
//Parts of it "Berat" finds the Service "Beratung". 
//Warning the Primary String may not be unique.
//		    
//<info>php %command.full_name% service --id=Picture:353</info>
//Same as by searching by Primary String but you can also provide the Column Name 
//where to Search. In this example the Picture Column is searched for Strings starting
//with 353.
//Colunm Name/Id Seperator is <info>":"</info>
//		    
//<info>php %command.full_name% service --id=1 --data="Name:New Name" --data="Description:New Description"</info>
//Add new Data to an existing Column. 
//Colunm Name/Data Seperator is <info>":"</info>
//
//<info>php %command.full_name% service --data="Name:New Name" --data="Description:New Description"</info>
//Add a new Service.
//		    
//<info>php %command.full_name% service --id=1 --data="Name:New Name" --file=/home/user/mypicture1.jpg</info>
//Updates the Name and the 1st Picture Field. 
//		    
//<info>php %command.full_name% service --id=1 --data="Name:New Name" --file=/home/user/mypicture1.jpg --file=/home/usermypicture2.jpg</info>
//Updates the Name, the 1st Picture Field and the 2nd Picture Field. 
//Use this carefully, its better to provide the correct column name for adding the File.
//The order you add files is relevant, the files are internally auto-mapped to the file columns in the
//given order.
//		    
//<info>php %command.full_name% information --id=4 --file=ImageSmall::/home/user/mypicture1.jpg --file=ImageLarge::/home/user/mypicture2.jpg</info>
//Better way to add Files by providing the Column names and Path.
//Colunm Name/Path Seperator is <info>"::"</info>
//		    
//
//EOT
//            )
	;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
	$output->writeln("\nDataHandler CLI - Cleaner\n");
	$this->dataCleaner = $this->getContainer()->get('c33s.datacleaner');

	$action	 = $input->getArgument('action');
	$force	 = $input->getOption('force');


	switch ($action)
	{
	    case 'clean':
		$this->deleteInvalid($force, $input, $output);
		break;
	    case 'show-invalid':
		$this->showInvalid($input, $output);
		break;
	    case 'show-missing':
		$this->showMissing($input, $output);
		break;
	    default:
		$this->showInvalid($input, $output);
		$this->showMissing($input, $output);
	}
    }

    protected function showInvalid(InputInterface $input, OutputInterface $output)
    {
	$invalidFiles = $this->dataCleaner->getInvalidFiles();
	$this->showList('Showing invalid Files which do not exist in the db.', $invalidFiles, 'error', $input, $output);
    }

    protected function showMissing(InputInterface $input, OutputInterface $output)
    {
	$missingFiles = $this->dataCleaner->getMissingFiles();
	$this->showList('Showing missing Files which do not exist on the fs.', $missingFiles, 'error', $input, $output);
    }

    protected function deleteInvalid($force = false, InputInterface $input, OutputInterface $output)
    {
	if ($force == true)
	{
	    $headline = 'Deleted the following files';
	}
	else
	{
	    $headline = 'Delete Preview';
	}
	$deletedFiles = $this->dataCleaner->deleteInvalidFiles($force);
	$this->showList($headline, $deletedFiles, 'info', $input, $output, true, true, $force);
    }

    protected function showList($headline, $list, $marker, InputInterface $input, OutputInterface $output, $addMarker = true, $hasForce = false, $force = false)
    {
	$output->writeln(sprintf("<comment>%s</comment>", $headline));

	if ($hasForce == true && $force == false)
	{
	    $output->writeln(sprintf("<info>system is running in preview mode. use --force to really delete</info>"));
	}

	if (count($list) == 0)
	{
	    $output->writeln('none.');
	}
	foreach ($list as $file) {
	    $output->writeln(sprintf("<%s>%s</%s>", $marker, $file, $marker));
	}
	$output->writeln('');
    }

}
