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
}