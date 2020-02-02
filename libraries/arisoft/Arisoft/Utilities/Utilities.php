<?php
/*
 *
 * @package		ARI Framework
 * @author		ARI Soft
 * @copyright	Copyright (c) 2011 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 *
 */
namespace Arisoft\Utilities;

defined('_JEXEC') or die;

use JURI;

class Utilities
{
    static function parseValueBySample($str, $sample)
    {
        return self::parseValue($str, gettype($sample));
    }

    static function parseValue($str, $type)
    {
        $retVal = $str;
        switch ($type)
        {
            case 'boolean':
                if (is_null($str))
                {
                    $retVal = false;
                }
                else
                {
                    $str = strtolower(trim($str));
                    if ($str == 'true' || $str == 'false')
                    {
                        $retVal = ($str == 'true');
                    }
                    else
                    {
                        $retVal = !empty($str);
                    }
                }
                break;

            case 'NULL':
                $retVal = null;
                break;

            case 'integer':
                $retVal = intval($str, 10);
                break;

            case 'double':
            case 'float':
                $retVal = floatval($str);
                break;
        }

        return $retVal;
    }
	
	static public function resolvePath($path)
	{
		if (!preg_match('#^(\/|\\\\|[A-z]\:(\/|\\\\))#i', $path))
			$path = JPATH_ROOT . DIRECTORY_SEPARATOR . $path;
		
		return $path;
	}
	
	static function absPath2Url($path)
	{		
		$absPath = str_replace('\\', '/', JPATH_ROOT);
		$path = str_replace('\\', '/', $path);
		if ($absPath != '/')
		{
			$path = str_replace($absPath, JURI::root(true), $path);
		}
		else
		{
			$path = JURI::root(true) . $path;
		}
		
		return $path;
	}
	
	static function absPath2Relative($path)
	{
		$absPath = str_replace('\\', '/', JPATH_ROOT);
		$path = str_replace('\\', '/', $path);
		if ($absPath != '/')
		{
			$path = str_replace($absPath, '', $path);
		}
		
		if (strpos($path, '/') === 0) $path = substr($path, 1);
		
		return $path;
	}
	
	static function extractContent($content, $pluginTag)
	{
		if (empty($pluginTag))
			return $content;
		
		$extractedContent = '';
		$matches = null;
		if (preg_match('/\{' . $pluginTag . '\}(.*)\{\/' . $pluginTag . '\}/s', $content, $matches))
		{
			$extractedContent = $matches[1];
		}

		return $extractedContent;
	}
}