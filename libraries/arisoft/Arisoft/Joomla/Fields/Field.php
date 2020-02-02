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
namespace Arisoft\Joomla\Fields;

defined('_JEXEC') or die;

use JFormField, JText;

abstract class Field extends JFormField
{
	public function get($key, $defaultValue = null)
	{
		$val = !is_null($this->element[$key]) ? $this->element[$key] : $defaultValue;
		
		return $val;
	}
	
	public function prepareMessage($message)
	{
		return JText::_($message);
	}
}