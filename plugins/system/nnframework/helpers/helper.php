<?php
/**
 * @package         NoNumber Framework
 * @version         18.12.3953
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2018 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory as JFactory;

require_once __DIR__ . '/cache.php';

class NNFrameworkHelper
{
	static function getPluginHelper(&$plugin, $params = null)
	{
		$hash = md5('getPluginHelper_' . $plugin->get('_type') . '_' . $plugin->get('_name') . '_' . json_encode($params));

		if (NNCache::has($hash))
		{
			return NNCache::get($hash);
		}

		if ( ! $params)
		{
			require_once __DIR__ . '/parameters.php';
			$params = NNParameters::getInstance()->getPluginParams($plugin->get('_name'));
		}

		require_once JPATH_PLUGINS . '/' . $plugin->get('_type') . '/' . $plugin->get('_name') . '/helper.php';
		$class = get_class($plugin) . 'Helper';

		return NNCache::set(
			$hash,
			new $class($params)
		);
	}

	static function isCategoryList($context)
	{
		$hash = md5('isCategoryList_' . $context);

		if (NNCache::has($hash))
		{
			return NNCache::get($hash);
		}

		// Return false if it is not a category page
		if ($context != 'com_content.category' || JFactory::getApplication()->input->get('view') != 'category')
		{
			return NNCache::set($hash, false);
		}

		// Return false if it is not a list layout
		if (JFactory::getApplication()->input->get('layout') && JFactory::getApplication()->input->get('layout') != 'list')
		{
			return NNCache::set($hash, false);
		}

		// Return true if it IS a list layout
		return NNCache::set($hash, true);
	}

	static function processArticle(&$article, &$context, &$helper, $method, $params = [])
	{
		if ( ! empty($article->description))
		{
			call_user_func_array([$helper, $method], array_merge([&$article->description], $params));
		}

		if ( ! empty($article->title))
		{
			call_user_func_array([$helper, $method], array_merge([&$article->title], $params));
		}

		if ( ! empty($article->created_by_alias))
		{
			call_user_func_array([$helper, $method], array_merge([&$article->created_by_alias], $params));
		}

		if (self::isCategoryList($context))
		{
			return;
		}

		// Process texts
		if ( ! empty($article->text))
		{
			call_user_func_array([$helper, $method], array_merge([&$article->text], $params));

			return;
		}

		if ( ! empty($article->introtext))
		{
			call_user_func_array([$helper, $method], array_merge([&$article->introtext], $params));
		}

		if ( ! empty($article->fulltext))
		{
			call_user_func_array([$helper, $method], array_merge([&$article->fulltext], $params));
		}
	}
}
