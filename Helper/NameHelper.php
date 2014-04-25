<?php

namespace c33s\CoreBundle\Helper;

class NameHelper
{
    protected $name;
    protected $nameCamelcase;
    protected $nameUnderscore;
    
    public function __construct($name,$inflector)
    {
	$this->name = ucfirst($name);
	$this->nameCamelcase = $inflector->camelize($name);
	$this->nameUnderscore = $inflector->underscore($name);
    }
    
    public function getName()
    {
	return $this->name;
    }
    
    public function camelcased()
    {
	return $this->nameCamelcase;
    }

    public function underscored()
    {
	return $this->nameUnderscore;
    }
    public function camelcase()
    {
	return $this->nameCamelcase;
    }

    public function underscore()
    {
	return $this->nameUnderscore;
    }

    public function __toString()
    {
	return $this->name;
    }
}