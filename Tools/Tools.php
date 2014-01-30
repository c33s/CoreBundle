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
	$lineCount = count($lines);
	
	for($i=0;$i<$lineCount;$i++)
	{
	    if (strstr($lines[$i],$stringToRemove))
	    {
		unset($lines[$i]);
	    }
	}
	file_put_contents($file, $lines);
    }
    
    /**
     * 
     * @param type $file
     * @param type $startLinePattern pattern to find in line of array, can be false if should start from beginning
     * @param type $endLinePattern
     * @param type $startOffset (offset relative from the found pattern)
     * @param type $endOffset (offset relative from the found pattern)
     * @param type $invert
     * @return type
     * @throws \Exception
     */
    public static function cropFileByLine($file, $startLinePattern = false, $endLinePattern = false, $startOffset = 0, $endOffset = 0, $invert = false)
    {
        if (is_array($file))
        {
            $lines = $file;
        }
        else
        {
            $lines = file($file);
        }
	
	
	
	
	
	$start = Tools::stringPosInArray($lines,$startLinePattern,0);
	if ($startLinePattern !== false && $start === false)
	{
	    throw new \Exception('Start line pattern "'.$startLinePattern.'" not found.');
	}
	if ($start === false)
	{
	    $start = 0;
	}
	$end = Tools::stringPosInArray($lines,$endLinePattern,$start+1);
	$start = $start + $startOffset;
	
	if ($endLinePattern !== false && $end === false)
	{
	    throw new \Exception('End line pattern "'.$endLinePattern.'" not found.');
	}	
	if ($end === false)
	{
	    $end = count($lines);
	}
	else
	{
	    $end = $end +1;
	}
	$end = $end + $endOffset;
	
	
	if (abs($start) > count($lines))
	{
	    throw new \Exception('Start beyond line count.');
	}
	if (abs($end) > count($lines))
	{
	    throw new \Exception('End beyond line count.');
	}
	
	
	
	
	$length = $end - $start;
	
	if ($invert === false)
	{
	    $minLength = 1;
	}
	else 
	{
	    $minLength = 0;
	}
	
	
	if ($length < $minLength)
	{
	    throw new \Exception("Length cannot be negative or zero (length: $length, start: $start, end: $end).");
	}
	
	if ($invert === false)
	{
	    $lines = array_slice($lines, $start, $length);
	}
	else 
	{
	    array_splice($lines, $start, $length);
	    $lines = array_values($lines);
	}
	
	if (!is_array($file))
	{
	    file_put_contents($file, $lines);
	}
	return $lines;
    }


    public static function addLineToFile($file,$stringToAdd,$stringAfterToInsert=false,$checkString = false)
    {
	$lines = file($file);
        
        if ($checkString === false)
        {
            $checkString = $stringToAdd;
        }
	
	if (!Tools::arrayLineHasString($lines, $checkString))
	{
	    if ($stringAfterToInsert !== false)
	    {
		$lineCount = count($lines);
		for($i=0;$i<$lineCount;$i++)
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
    
    public static function stringPosInArray($array,$string,$offset = 0, $trim = true)
    {
	if ($string === false || $string === null || empty($string))
        {
            return false;
        }
	
	if ($array === false || $array === null || empty($array))
        {
            return false;
        }
	
	if ($trim == true)
	{
	    $string = trim($string);
	}
	
	$lineCount = count($array);
	for($i=0+$offset;$i<$lineCount;$i++)
	{
	    $result = strpos($array[$i],$string);
	    if ($result !== false)
	    {
		return $i;
	    }
	}
	
	return false;
    }
    
    public static function arrayLineHasString($array,$string)
    {
	if (Tools::stringPosInArray($array, $string) !== false)
	{
	    return true;
	}
	return false;
    }


    public static function insertBetweenArray($element,$position,$array)
    {
	array_splice($array, $position, 0, $element);
	return $array;
    }
}