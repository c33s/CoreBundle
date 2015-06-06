<?php

namespace C33s\CoreBundle\Twig;

class DataHandlerHashDirExtension extends \Twig_Extension
{
    /**
    * @var \Twig_Environment
    */
    protected $environment;
    
    public function __construct(\C33s\CoreBundle\DataHandler\DataHandler $dataHandler)
    {
	$this->dataHandler = $dataHandler;
    }
    
    /**
    * {@inheritDoc}
    */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;

    }

    public function getName()
    {
	return 'c33s_hashdir_extension';
    }

    public function getFilters()
    {
	return array(
	    //new \Twig_SimpleFilter('hashdir', array($this, 'hashDirFilter'), array('is_safe' => array('html'))),
	    new \Twig_SimpleFilter('hashdir', array($this, 'hashDirFilter')),
	);
    }

    public function hashDirFilter($object,$field)
    {
	$this->dataHandler->init($object);
	$return = $this->dataHandler->getFilePath($field, false);
	
	return $return;
    }
}
