<?php

namespace c33s\CoreBundle\Util;

use c33s\CoreBundle\Util\InflectorInterface;
use c33s\CoreBundle\Exception\MethodNotImplementedException;
use Doctrine\Common\Inflector\Inflector;


class DoctrineInflector implements InflectorInterface
{
    public function pluralize($word)
    {
	return Inflector::pluralize($word);
    }

    public function singularize($word)
    {
	return Inflector::singularize($word);
    }

    public function titleize($word, $uppercase = '')
    {
	throw new MethodNotImplementedException;
    }

    public function camelize($word)
    {
	return Inflector::classify($word);
    }

    public function underscore($word)
    {
	return Inflector::tableize($word);
    }

    public function humanize($word, $uppercase = '')
    {
	throw new MethodNotImplementedException;
    }

    public function variablize($word)
    {
	return lcfirst(Inflector::classify($word));
    }

    public function tableize($class_name)
    {
	return $this->pluralize($this->underscore($class_name));
    }

    public function classify($table_name)
    {
	return $this->camelize($this->singularize($table_name));
    }

    public function ordinalize($number)
    {
	throw new MethodNotImplementedException;
    }
}
