<?php
/*
 *
 * @package		ARI Framework
 * @author		ARI Soft
 * @copyright	Copyright (c) 2011 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 *
 */
namespace Arisoft\Parameters;

defined('_JEXEC') or die;

define('ARI_PARAMETERS_DEFAULT_NAMESPACE', '_default');

use Arisoft\Utilities\Utilities as Utils;

class Helper
{
    static function getRootParameters($paramsTree, $defaultNS = ARI_PARAMETERS_DEFAULT_NAMESPACE)
    {
        $rootParams = null;

        if (isset($paramsTree[$defaultNS]))
            $rootParams = $paramsTree[$defaultNS];

        return $rootParams;
    }

	static function flatParametersToTree($flatParams, $splitter = '_', $defaultNS = ARI_PARAMETERS_DEFAULT_NAMESPACE)
	{
		$params = array($defaultNS => array());

		if (empty($flatParams))
			return $params;

		if (!is_array($flatParams))
		{
			if (is_object($flatParams) && method_exists($flatParams, 'toArray'))
				$flatParams = $flatParams->toArray();
			else 
				$flatParams = (array)$flatParams;
		}

		$currentNS = null;
		foreach ($flatParams as $key => $value)
		{
			$nsList = explode($splitter, $key);
			$paramKey = array_pop($nsList);
			$cnt = count($nsList);
			if ($cnt == 0)
			{
				$currentNS =& $params[$defaultNS];
			}
			else
			{
				$currentNS =& $params;
				for ($i = 0; $i < $cnt; $i++)
				{
					$ns = $nsList[$i];
					if (!array_key_exists($ns, $currentNS))
					{
						$currentNS[$ns] = array();
					}
						
					$currentNS =& $currentNS[$ns];
				}
			}

			$currentNS[$paramKey] = $value;
		}

		return $params;
	}
	
	static function getUniqueOverrideParameters($srcParams, $overrideParams, $caseInsensitive = false)
	{
		$uniqueParams = array();
		if (is_null($overrideParams))
			$overrideParams = array();
		foreach ($srcParams as $srcKey => $srcValue)
		{
			if (is_array($srcValue))
			{
				if (isset($overrideParams[$srcKey]) || ($caseInsensitive && isset($overrideParams[strtolower($srcKey)])))
				{
					$subParams = self::getUniqueOverrideParameters(
						$srcValue, 
						isset($overrideParams[$srcKey]) ? $overrideParams[$srcKey] : $overrideParams[strtolower($srcKey)],
						$caseInsensitive);
					if (count($subParams) > 0)
						$uniqueParams[$srcKey] = $subParams;
				}
			}
			else if (array_key_exists($srcKey, $overrideParams) || ($caseInsensitive && array_key_exists(strtolower($srcKey), $overrideParams)))
			{
				$overrideValue = Utils::parseValueBySample(
					isset($overrideParams[$srcKey]) ? $overrideParams[$srcKey] : $overrideParams[strtolower($srcKey)], 
					$srcValue
                );

				if ($overrideValue != $srcValue)
					$uniqueParams[$srcKey] = $overrideValue; 
			}
		}
		
		return $uniqueParams;
	}
	
	static function removeSameParameters($srcParams, $params)
	{
		$retParams = array();
		
		foreach ($srcParams as $srcKey => $srcValue)
		{
			if (is_array($srcValue))
			{
				if (!isset($params[$srcKey]))
					$retParams[$srcKey] = $srcValue;
				else
				{
					$subParams = self::removeSameParameters($srcValue, $params[$srcKey]);
					if (count($subParams) > 0)
						$retParams[$srcKey] = $subParams;
				}
			}
			else if (!isset($params[$srcKey]) || $params[$srcKey] !== $srcValue)
			{
				$retParams[$srcKey] = $srcValue;
			} 
		}

		return $retParams;
	}

    static function prepareMultipleInlineParameter($val, $key = null, $unique = true)
    {
        $preparedVal = array();
        if (is_array($val))
        {
            foreach ($val as $item)
            {
                $itemVal = trim($item->$key);
                if (!empty($itemVal))
                    $preparedVal[] = $itemVal;
            }
        }
        else
        {
            $val = trim($val);

            $val = str_replace(array("\r", "\n"), array('', ';'), $val);
            $val = explode(';', $val);
            array_walk($val, 'trim');
            foreach ($val as $item)
            {
                if (empty($item))
                    continue;

                $preparedVal[] = $item;
            }
        }

        if ($unique)
            $preparedVal = array_unique($preparedVal);

        return $preparedVal;
    }
}