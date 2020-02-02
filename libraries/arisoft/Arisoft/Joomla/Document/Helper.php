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
namespace Arisoft\Joomla\Document;

defined('_JEXEC') or die;

use JFactory;

class Helper
{
	static function addCustomTagsToDocument($tags)
	{
		if (empty($tags)) 
			return ;

		$app = JFactory::getApplication();

		$content = $app->getBody();
		$content = preg_replace('/(<\/head\s*>)/i', join('', $tags) . '$1', $content, 1);

		$app->setBody($content); 
	}
}