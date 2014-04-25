<?php

namespace c33s\CoreBundle\Twig;

use c33s\CoreBundle\Util\InflectorInterface;


class QuickFilterExtension extends \Twig_Extension
{
    /**
    * @var \Twig_Environment
    */
    protected $environment;
    protected $inflector;
    
    public function __construct(InflectorInterface $inflector)
    {
	$this->inflector = $inflector;
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
	return 'c33s_quick_filter_extension';
    }

    public function getFilters()
    {
	return array(
	    new \Twig_SimpleFilter('camelize', array($this, 'camelcaseFilter')),
	    new \Twig_SimpleFilter('underscorize', array($this, 'underscoreFilter')),
	    new \Twig_SimpleFilter('camelcase', array($this, 'camelcaseFilter')),
	    new \Twig_SimpleFilter('underscore', array($this, 'underscoreFilter')),
	);
    }

    public function camelcaseFilter($word)
    {
	return $this->inflector->camelize($word);
    }
    public function underscoreFilter($word)
    {
	return $this->inflector->underscore($word);
    }
}
