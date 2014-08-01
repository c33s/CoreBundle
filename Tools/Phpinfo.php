<?php

namespace C33s\CoreBundle\Tools;

class Phpinfo
{
    public $phpinfo;
    
    public function __construct()
    {
	$this->buildAndParse();
    }
    
    protected function buildAndParse()
    {
	$matches = $this->build();
	$this->phpinfo = $this->parse($matches);
    }
    
    public function get()
    {
	return $this->phpinfo;
    }
    
    protected function parse($refmatches)
    {
	$phpinfo = array();
	$matches = $refmatches;
	foreach($matches as $match)
	{
	    if(strlen($match[1]))
	    {
		$phpinfo[$match[1]] = array();
	    }
	    elseif(isset($match[3]))
	    {
		$keys = array_keys($phpinfo);
		$key = end($keys);
		$phpinfo[$key][$match[2]] = isset($match[4]) ? array($match[3], $match[4]) : $match[3];
	    }
	    else
	    {
		$keys = array_keys($phpinfo);
		$key = end($keys);
		$phpinfo[$key][]= $match[2];
	    }
	}
	
	return $phpinfo;
    }
    protected function build()
    {
	$matches = null;
	ob_start();
	phpinfo();
	$pattern = '#(?:<h2>(?:<a name=".*?">)?(.*?)(?:</a>)?</h2>)|(?:<tr(?: class=".*?")?><t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>)?)?</tr>)#s';
	preg_match_all($pattern, ob_get_clean(), $matches, PREG_SET_ORDER);
	
	return $matches;	
    }
    
}