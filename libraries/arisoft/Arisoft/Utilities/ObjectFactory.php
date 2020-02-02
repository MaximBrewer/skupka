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

class ObjectFactory
{
	static public function getObject($name, $ns, $params = array())
	{
		$name = ucfirst(preg_replace('/[^A-Z_]/i', '', $name));
		$className = $ns . '\\' . $name;
		
		$obj = null;
		
		if (count($params) == 0)
		{
			$obj = new $className();
		}
		else
		{		
			$reflection = new \ReflectionClass($className); 
			$obj = $reflection->newInstanceArgs($params);
		}
		
		return $obj;
	}
}