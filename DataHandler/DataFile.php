<?php

namespace c33s\CoreBundle\DataHandler;

use Symfony\Component\HttpFoundation\File\File;

use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * picture attachment
 * service picture
 * information image_small, image_large
 */
class DataFile extends File
{
    protected $hash = null;
    protected $directory = '';
    protected $path = null;
    protected $directoryLevels = null;
    protected $subDirectory = null;
    //protected $baseDir = '%kernel.root_dir%/../web/upload';
    protected $baseDir = null;
    protected $levels;
    
    public function __construct($path, $baseDir, $levels, $subDirectory = null, $initFromDatabase=false)
    {
        $this->baseDir = $baseDir;
        $this->subDirectory = $subDirectory;
        $this->levels = $levels;
        
        if ($initFromDatabase === false)
        {
            parent::__construct($path,true);
            $this->path = $path;
            $this->calculateHash();
        }
        else
        {
            $this->hash = pathinfo($path,PATHINFO_FILENAME);
            $this->path = $this->getDirectory().$path;
        }
    }
    
    public function __toString()
    {
        return $this->getRelativeDirectory().DIRECTORY_SEPARATOR.$this->hash;
        
    }
    
    protected function calculateHash()
    {
        $this->hash = sha1_file($this->path).'.'.$this->getExtension();
    }
    
    public function calculateDirectories()
    {
        $dir = '';
        
        for($i=0;$i<$this->levels;$i++)
        {
            $dir .= $this->hash[$i].DIRECTORY_SEPARATOR;
        }
        
        return $this->getSubdirectoryString($this->subDirectory).$dir;
    }
    
    public function getDirectory()
    {
        return $this->baseDir.DIRECTORY_SEPARATOR.$this->calculateDirectories();
    }
    
    public function getRelativeDirectory()
    {
        return $this->calculateDirectories();
    }
    
    public function getSubdirectoryString($subdirectory=null)
    {
        if ($subdirectory != null && $subdirectory != '')
        {
            return  $subdirectory.DIRECTORY_SEPARATOR;
        }
        else
        {
            return '';
        }
    }
    
    public function getHash()
    {
        return $this->hash;
    }
    
    
    public function move($directory = null, $name = NULL)
    {
        if ($directory != null || $name != null)
        {
            throw new \InvalidArgumentException('the call to move must not contain parameters, they are only added to prevent "Runtime Notice: Declaration of c33s\CoreBundle\DataHandler\DataFile::move() should be compatible with Symfony\Component\HttpFoundation\File\File::move($directory, $name = NULL)"');
        }
        
        $directory = $this->getDirectory();
        parent::move($directory, $this->hash);
        
        return $this->hash;
    }
    
    public function copy()
    {
        $target = $this->getTargetFile($this->getDirectory(), $this->hash);

        if (!@copy($this->getPathname(), $target)) {
            $error = error_get_last();
            throw new FileException(sprintf('Could not copy the file "%s" to "%s" (%s)', $this->getPathname(), $target, strip_tags($error['message'])));
        }

        @chmod($target, 0666 & ~umask());

        return $this->hash;
    }
}
