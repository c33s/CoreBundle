<?php

namespace C33s\CoreBundle\Twig;

class ConfigVariablesExtension extends \Twig_Extension
{
    /**
    * @var \Twig_Environment
    */
    protected $environment;
    
    public function __construct($config)
    {
	$this->config = $config;
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
	return 'c33s_config_variables_extension';
    }

    public function getGlobals() 
    {
	$config = $this->config;
	
        return array('c33s_core_config' => $config);
    }

    protected function getConfig()
    {
    }
}
