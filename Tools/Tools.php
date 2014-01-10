<?php

namespace c33s\CoreBundle\Tools;

class Tools
{
    /**
     * Converts human readable file size (e.g. 10 MB, 200.20 GB) into bytes.
     *
     * @param string $str
     * @return int the result is in bytes
     * @author Svetoslav Marinov
     * @author http://slavi.biz
     */
    public static function filesizeToBytes($str) 
    {
        $bytes = 0;

        $bytes_array = array(
            'B' => 1,
            'KB' => 1024,
            'MB' => 1024 * 1024,
            'GB' => 1024 * 1024 * 1024,
            'TB' => 1024 * 1024 * 1024 * 1024,
            'PB' => 1024 * 1024 * 1024 * 1024 * 1024,
        );

        $bytes = floatval($str);

        if (preg_match('#([KMGTP]?B)$#si', $str, $matches) && !empty($bytes_array[$matches[1]])) {
            $bytes *= $bytes_array[$matches[1]];
        }

        $bytes = intval(round($bytes, 2));

        return $bytes;
    } 
    
    public static function removeLineFromFile($file,$stringToRemove)
    {
	$lines = file($file);
	
	for($i=0;$i<count($lines);$i++)
	{
	    if (strstr($lines[$i],$stringToRemove))
	    {
		unset($lines[$i]);
	    }
	}
	file_put_contents($file, $lines);
    }
    public static function addLineToFile($file,$stringToAdd,$stringAfterToInsert=false)
    {
	$lines = file($file);
	
	if (!Tools::arrayLineHasString($lines, $stringToAdd))
	{
	    if ($stringAfterToInsert !== false)
	    {
		for($i=0;$i<count($lines);$i++)
		{
		    if (strstr($lines[$i],$stringAfterToInsert))
		    {
			$lines = Tools::insertBetweenArray($stringToAdd,$i+1,$lines);
			break;
		    }
		}
	    }
	    else
	    {
		$lines[]=$stringToAdd;
	    }
	    file_put_contents($file, $lines);
	}

    }
    
    public static function arrayLineHasString($array,$string)
    {
	foreach ($array as $line)
	{
	    if (strstr($line,$string))
	    {
		return true;
	    }
	}
	
	return false;
    }


    public static function insertBetweenArray($element,$position,$array)
    {
	array_splice($array, $position, 0, $element);
	return $array;
    }
}