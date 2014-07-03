<?php

namespace c33s\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DataHandlerCommand extends ContainerAwareCommand
{
    // c33s:file service 1 c:\test.txt
    // c33s:file service -> liste
    
    protected $files = array();
    
    protected function configure()
    {
         $this
            ->setName('c33s:data')
            ->setDescription('Queries and updates the Database including File insertion')
            ->addArgument('table', InputArgument::REQUIRED, 'The Databaase Table to alter rows in.' )
            ->addArgument('data', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, 'Add data to update or enter.' )
            ->addOption('id',null, InputOption::VALUE_OPTIONAL, 'Int Id or Primary String of Row to select.')
            ->addOption('file',array(), InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL, 'Add a file via path to the Row.')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command can query the database and allows you
to add or update Fields.

<info>php %command.full_name% service</info>
As no id is given a Table of all Columns is shown.
                    
<info>php %command.full_name% service --id=1</info>
With a id given, only the Service with the given id is shown.
                    
<info>php %command.full_name% service --id=Berat</info>
Providing a non-numeric id searches for the service by its Primary String or even
Parts of it "Berat" finds the Service "Beratung".
Warning the Primary String may not be unique.
                    
<info>php %command.full_name% service --id=Picture:353</info>
Same as by searching by Primary String but you can also provide the Column Name
where to Search. In this example the Picture Column is searched for Strings starting
with 353.
Colunm Name/Id Seperator is <info>":"</info>
                    
<info>php %command.full_name% service --id=1 --data="Name:New Name" --data="Description:New Description"</info>
Add new Data to an existing Column.
Colunm Name/Data Seperator is <info>":"</info>

<info>php %command.full_name% service --data="Name:New Name" --data="Description:New Description"</info>
Add a new Service.
                    
<info>php %command.full_name% service --id=1 --data="Name:New Name" --file=/home/user/mypicture1.jpg</info>
Updates the Name and the 1st Picture Field.
                    
<info>php %command.full_name% service --id=1 --data="Name:New Name" --file=/home/user/mypicture1.jpg --file=/home/usermypicture2.jpg</info>
Updates the Name, the 1st Picture Field and the 2nd Picture Field.
Use this carefully, its better to provide the correct column name for adding the File.
The order you add files is relevant, the files are internally auto-mapped to the file columns in the
given order.
                    
<info>php %command.full_name% information --id=4 --file=ImageSmall::/home/user/mypicture1.jpg --file=ImageLarge::/home/user/mypicture2.jpg</info>
Better way to add Files by providing the Column names and Path.
Colunm Name/Path Seperator is <info>"::"</info>
                    

EOT
            )
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("\nData Handler CLI\n");
        
        $table = $input->getArgument('table');
        $id = $input->getOption('id');
        $data = $input->getArgument('data');
        $files = $input->getOption('file');
        

        $this->init($table,$id);
        
        if (empty($data) && empty($files))
        {
            if ($id == null)
            {
                $this->renderList($input, $output);
            }
            else
            {
                $this->renderOne($input, $output);
            }
        }
        else
        {
            if (!empty($data))
            {
                $this->setData($data);
                $output->writeln("added data");
            }

            if (!empty($files))
            {
                $this->addFiles($files);
                $output->writeln("added files");
            }
            
            $this->save();
            $output->writeln("saved object");
        }
        
        //var_dump($input->getArgument('data'));
        //$this->init($table,$id);
        
        //var_dump($this->dataHandler->dumpObject());
    }
    
    protected function save()
    {
        $this->dataHandler->save();
    }
    
    protected function init($table,$id)
    {
        $this->dataHandler = $this->getContainer()->get('c33s.datahandler');
        $this->dataHandler->init($table, $id);
    }
    
    protected function renderList(InputInterface $input, OutputInterface $output)
    {
        $objects = $this->dataHandler->getObjectList();
        $objects = $objects->toArray();
        $this->renderTable($input, $output, $objects);
    }
    
    protected function renderOne(InputInterface $input, OutputInterface $output)
    {
        $objects = $this->dataHandler->getObject();
        $objects = array($objects->toArray());
        $this->renderTable($input, $output, $objects);
    }
    
    protected function cleanObject(&$obj,&$headers)
    {
        foreach ($obj as $key => &$col )
        {
            if (is_object($col))
            {

                unset($obj[$key]);
            }
            else
            {
                $headers[$key] = $key;
                
                $col = $this->trim($col);
                $col = $this->toAscii($col);
            }
        }
    }
    
    protected function toAscii($col)
    {
        //echo $col."\n";
        return iconv("UTF-8", "CP437//TRANSLIT//IGNORE", $col);
    }
    
    protected function trim($col,$maxLength=50)
    {
        $col = trim($col);
        if (strlen($col)>$maxLength)
        {
            $col = substr($col, 0,50).' ...';
        }
            
        return $col;
    }
    
    protected function renderTable(InputInterface $input, OutputInterface $output, $objects)
    {
        
        $headers = array();
        $i=0;
        foreach ($objects as &$obj)
        {
            $this->cleanObject($obj,$headers);
            $i++;
        }

        $table = $this->getHelperSet()->get('table');
        $table
            ->setHeaders($headers)
            ->setRows($objects)
        ;
        $table->render($output);
    }
    
    protected function addFiles($files)
    {
        foreach ($files as $file)
        {
            if (strpos($file,'::') !== false)
            {
                list($field, $file) = explode("::", $file, 2);
            }
            else
            {
                $field = null;
                $file = $file;
            }
            $this->dataHandler->addFile($file,$field);
        }
    }
    
    protected function setData($inData)
    {
        $data = $this->buildDataArray($inData);
        $this->dataHandler->setData($data);
    }
    
    protected function buildDataArray($inData)
    {
        $data=array();
        
        foreach ($inData as $dataRow)
        {
            list($key,$value) = explode(":", $dataRow, 2);
            $data[$key] = $value;
        }
        
        return $data;
    }
}
