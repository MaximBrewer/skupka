<?php
/**
 * @package         Sliders
 * @version         6.0.8
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2016 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

$items = slidersGetItemsDataBySelection();

echo json_encode($items);

die();

function slidersGetItemsDataBySelection()
{
	$string = JFactory::getApplication()->input->getString('selection');

	if (empty($string))
	{
		return array();
	}

	require_once JPATH_LIBRARIES . '/regularlabs/helpers/parameters.php';
	require_once JPATH_LIBRARIES . '/regularlabs/helpers/tags.php';
	require_once JPATH_PLUGINS . '/system/sliders/helpers/helpers.php';

	$params = RLParameters::getInstance()->getPluginParams('sliders');

	$params->comment_start = '<!-- START: Sliders -->';
	$params->comment_end   = '<!-- END: Sliders -->';

	$params->tag_open  = trim(preg_replace('#[^a-z0-9-_]#si', '', $params->tag_open));
	$params->tag_close = trim(preg_replace('#[^a-z0-9-_]#si', '', $params->tag_close));

	$params->tag_link = isset($params->tag_link) ? $params->tag_link : 'tablink';
	$params->tag_link = trim(preg_replace('#[^a-z0-9-_]#si', '', $params->tag_link));


	$helpers = PlgSystemSlidersHelpers::getInstance($params);
	$helper  = $helpers->get('replace');

	$sets = $helper->getSets($string, true);

	if (empty($sets))
	{
		return array();
	}

	$items = array_shift($sets);

	$contents = preg_replace($helper->params->regex, '[:BREAK:]', $string);
	$contents = explode('[:BREAK:]', $contents);

	if (empty($contents))
	{
		return array();
	}

	array_shift($contents);

	foreach ($items as $i => &$item)
	{
		if (!empty($item->noscroll))
		{
			$item->scroll = false;
			unset($item->noscroll);
		}

		$item->content = isset($contents[$i]) ? $contents[$i] : '';

		$item = (array) $item;
	}

	return $items;
}
