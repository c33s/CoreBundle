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
    
    public static function removeLines($file, $startLinePattern = false, $endLinePattern = false)
    {
         if (is_array($file))
        {
            $lines = $file;
        }
        else
        {
            $lines = file($file);
        }
        
        
        
        
        
        
        
        if (!is_array($file))
        {
            file_put_contents($file, $lines);
        }
        
        return $lines;  
    }
    
    /**
     * 
     * @param type $file
     * @param type $startLinePattern true if should start at line 0
     * @param type $endLinePattern
     * @param type $includeStart
     * @param type $includeEnd
     */
    public static function cropFileByLine($file, $startLinePattern = false, $endLinePattern = false, $includeStart = false, $includeEnd = false, $invert = false)
    {
        if (is_array($file))
        {
            $lines = $file;
        }
        else
        {
            $lines = file($file);
        }
        
        
        $started = false;
        $ended = false;
        //includeLineContainingPattern

        if (
                $startLinePattern === true 
                || Tools::arrayLineHasString($lines, $startLinePattern)
                || ($endLinePattern !== false && Tools::arrayLineHasString($lines, $endLinePattern))
            )
	{
            if ($startLinePattern === true)
            {
                $started = 0;
            }

            for($i=0;$i<count($lines);$i++)
            {
            //var_dump("in for");
                if (strstr($lines[$i],$startLinePattern))
                {
                   $started = $i;
                   //var_dump("$i start");
                    if ($includeStart === false)
                    {
                        //var_dump("$i start +1");
                        $started = $started + 1;
                    }
                    
                    
                }
                //var_dump("strpos", strpos($lines[$i],$endLinePattern,$started+1), $lines[$i],$i,$started+1,$endLinePattern);
                if ($started != $i && false !== $endLinePattern && strstr($lines[$i],$endLinePattern))
                //if ($started != $i && false !== $endLinePattern && strpos($lines[$i],$endLinePattern,$started+1))
                {
                   $ended = $i;
                   //var_dump("$i end");
                   if ($includeEnd === true)
                    {
                       //var_dump("$i end +1");
                       $ended = $ended + 1; 
                    }
                }
            } //endfor
            
            if ($endLinePattern === false || $ended === false)
            {
                //if no endline pattern, take all
                $ended = count($lines);
                //var_dump("ended: $ended ".count($lines)." $started");
            }
            
            $length = $ended - $started;
//            if ($length < 0)
//            {
//                var_dump("negative value");
//                $started = $started + $length;
//                //$ended = $ended + abs($length);
//                $length = abs($length);
//            }
            
            if ($invert === false)
            {
                $lines = array_slice($lines, $started, $length);
            }
            else 
            {
                array_splice($lines, $started, $length);
            }
            
            //var_dump($lines, $started,$ended, $length);
            
            
            if (!is_array($file))
            {
                file_put_contents($file, $lines);
            }
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
        if ($string === false )
        {
            return false;
        }
	foreach ($array as $line)
	{
	    if (false !== strpos($line,trim($string)))
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