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

use \Joomla\Utilities\ArrayHelper as ArrayHelper;

class Imagegallery extends Base
{
	public function prepareItem($item, $mapping)
	{
		$preparedItem = array();
		
		$image = $item['image'];

		$preparedItem[$mapping->title] = ArrayHelper::getValue($item, 'Title');
		$preparedItem[$mapping->description] = ArrayHelper::getValue($item, 'Description');
		$preparedItem[$mapping->url] = $image['url'];
		$preparedItem[$mapping->width] = $image['w'];
		$preparedItem[$mapping->height] = $image['h'];
		
		if (!empty($item['thumb']['thumb']))
		{
			$thumb = $item['thumb']['thumb'];
			
			$preparedItem[$mapping->previewUrl] = $thumb['url'];
			$preparedItem[$mapping->previewWidth] = $thumb['w'];
			$preparedItem[$mapping->previewHeight] = $thumb['h'];
		}

		return $preparedItem;
	}
	
	public function prepareHiddenItem($item, $mapping)
	{
		return $this->prepareItem($item, $mapping);
	}
}