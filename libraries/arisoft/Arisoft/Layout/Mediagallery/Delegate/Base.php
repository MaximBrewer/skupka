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
namespace Arisoft\Layout\Mediagallery\Delegate;

defined('_JEXEC') or die;

class Base
{
	public function prepareItem($item, $mapping)
	{
		return $item;
	}
}