<?php

namespace c33s\CoreBundle\DataHandler;

#use Symfony\Component\HttpFoundation\File\File;

/**
 * picture attachment
 * service picture
 * information image_small, image_large
 */
//use c33s\ModelBundle\Model\Service;
//use c33s\ModelBundle\Model\ServiceQuery;

class DataHandler
{
    protected $data;
    protected $object;
    protected $dataBaseObjectNamespace = 'c33s\\ModelBundle\\Model\\';
    protected $dataBaseDir = null;
    protected $dataWebDir = null;
    protected $table = null;
    protected $files = array();
    
    protected $queryClassname;
    protected $classClassname;
    
    protected $fileCounter = 0;
    
    protected $map = array ();
    protected $classMap = array();
    protected $config;
    
    public function __construct($dataBaseDir,$dataWebDir,$config)
    {
        $this->dataBaseDir = $dataBaseDir;
        $this->dataWebDir = $dataWebDir;
        $this->map = $config['db_maps'];
        $this->levels = $config['levels'];
        $this->classMap = $config['class_maps'];
        $this->config = $config;
    }
    
    public function getBaseDir()
    {
        return $this->dataBaseDir;
    }
    public function getWebDir()
    {
        return $this->dataWebDir;
    }
    public function getConfig()
    {
        return $this->config;
    }
    
    public function getTableQueryObjects()
    {
        $tableObjects = array();
        foreach ($this->classMap as $class => $index)
        {
            $queryClassname = $class.'Query';
            $tableObjects[] = $queryClassname::create()->includeUnpublished();
        }
//            $objects = ->find();
        
        return $tableObjects;
    }
    public function getFieldListByObject($object)
    {
        $index = $this->classMap[get_class($object)];
        $fields = $this->map[$index]['file_fields'];
        
        return $fields;
    }


    protected function setTableName($table,$autofixTableName = true)
    {
        if($autofixTableName == true)
        {
            $table = ucfirst(strtolower($table));
        }
        $this->table = $table;
        $this->queryClassname = $this->dataBaseObjectNamespace.$this->table.'Query';
        $this->classClassname = $this->dataBaseObjectNamespace.$this->table;
    }


    public function init($table, $id=null, $autofixTableName = true)
    {
        if ($this->checkIfClassValid($table))
        {
            $this->initObjectByObject($table);
        }
        else
        {
            $this->setTableName($table,$autofixTableName);
            $this->initObjectById($id);
        }
    }
    
    protected function checkIfClassValid($object)
    {
        if (!is_object($object)) { return false; }
        $class = get_class($object);
        if (strpos($class,'c33s\ModelBundle\Model') !== false ) return true;
        
        return false;
    }
    
    
    
    protected function initObjectByObject($object)
    {
        $this->object = $object;
    }
    protected function initObjectById($id)
    {
        $queryClassname = $this->queryClassname;
        $classClassname = $this->classClassname;
        
        if ($id != null)
        {
            if (is_int($this->convertToIntIfInt($id)))
            {
                $this->object = $queryClassname::create()->includeUnpublished()->findOneById($id);
            }
            else
            {
                if (strpos($id,':') !== false)
                {
                    list($column, $value) = explode(":", $id, 2);
                }
                else
                {
                    $column = $this->map[strtolower($this->table)]['primary_string'];
                    $value = $id;
                }
                $filterMethod = 'filterBy'.$column;
                $this->object = $queryClassname::create()->includeUnpublished()->$filterMethod($value.'%')->findOne();
            }
        }
        else
        {
            $this->object = new $classClassname();
        }
    }
    
    protected function convertToIntIfInt($id)
    {
        if ($this->isIdInt($id))
        {
            return $id = (int) $id;
        }
        
        return $id;
    }
    protected function isIdInt($id)
    {
        preg_match('/^[0-9]+$/', $id, $matches);
        if (count($matches) > 0)
        {
            return true;
        }
        
        return false;
    }
    
    public function getObject()
    {
        return $this->object;
    }
    public function getObjectList()
    {
        $queryClassname = $this->queryClassname;
        
        return $queryClassname::create()->includeUnpublished()->find();
    }
    
    public function setData(Array $data=array())
    {
        $this->object->fromArray($data);
    }
    
    public function save()
    {
        foreach ($this->files as $map => $file)
        {
            if (get_class($file) == 'c33s\CoreBundle\DataHandler\DataFile')
            {
                $fileHash = $file->copy();
                //TODO: UNlink old file here
                $method = 'set'.ucfirst($map);
                $this->object->{$method}($fileHash);
            }
        }
        
        return $this->object->save();
    }
    
    public function addFile($path,$field=null)
    {
        if ($field === null) { $field = $this->map[strtolower($this->table)]['file_fields'][$this->fileCounter]; }
        
        $subdirectory = $this->map[strtolower($this->table)]['directory'];
        $this->files[$field] = new DataFile($path, $this->dataBaseDir, $this->levels, $subdirectory);
        $this->fileCounter++;
        
        return $this->files[$field];
    }
    
    protected function getFileNameByField($field)
    {
        $method = 'get'.ucfirst($field);
        $file = $this->object->$method();
        return $file;
    }
    
    public function getFilePath($filefield, $absolute = true)
    {
        $mapIndex = $this->getDbMapIndexFromObject();
        $subdirectory = $this->map[$mapIndex]['directory'];
        
        $file = $this->getFileNameByField($filefield);
        //var_dump($file);
        if ($file != null)
        {
            //($path, $baseDir, $levels, $subDirectory = null, $initFromDatabase=false)
            $dataFile = new DataFile($file, $this->dataBaseDir, $this->levels, $subdirectory, true);
            if ($absolute === true)
            {
                $directory = $dataFile->getDirectory();
                return $directory.$file;
            }
            else
            {
                $directory = $dataFile->getRelativeDirectory();
                return $this->dataWebDir.DIRECTORY_SEPARATOR.$directory.$file;
            }
        }
        
        return null;
    }
    
    protected function getDbMapIndexFromObject()
    {
        $class = get_class($this->object);
        
        return $this->lookupDbMapIndexFromClass($class);
    }
    
    protected function lookupDbMapIndexFromClass($class)
    {
        if (array_key_exists($class,$this->classMap))
        {
            return $this->classMap[$class];
        }
        else
        {
            throw new \InvalidArgumentException('Invalid class. Only Class allowed which is defined in c33sCoreBundle/Resources/config.yml in class_maps. Input was: '.$class);
        }
    }
}
