<?php
/*
 * ARI Framework
 *
 * @package		ARI Framework
 * @version		1.0.0
 * @author		ARI Soft
 * @copyright	Copyright (c) 2009 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 *
 */
namespace Arisoft\Template;

defined('_JEXEC') or die;

class Filter
{
	public function parse($val, $params)
	{
		return $val;
	}
	
	protected function prepareParams($params, $defaultValues)
	{
		$preparedParams = array();
		foreach ($defaultValues as $idx => $defaultValue)
		{
			$preparedParams[] = isset($params[$idx]) ? $params[$idx] : $defaultValue;
		}
		
		return $preparedParams;
	}
}